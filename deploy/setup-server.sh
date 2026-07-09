#!/usr/bin/env bash
set -euo pipefail

# ============================================================
# Consultoria SaaS - Server Bootstrap Script
# Uso: bash setup-server.sh
# Executar como root em um servidor Ubuntu 22.04+ limpo
# ============================================================

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; NC='\033[0m'
info() { echo -e "${GREEN}[INFO]${NC} $1"; }
warn() { echo -e "${YELLOW}[WARN]${NC} $1"; }

DOMAIN="${1:-consultoriasaas.com.br}"
WP_ADMIN="${2:-admin}"
WP_EMAIL="${3:-admin@consultoriasaas.com.br}"

if [ "$EUID" -ne 0 ]; then
    echo "Execute como root: sudo bash setup-server.sh"
    exit 1
fi

info "=== Instalando LAMP Stack ==="

apt update && apt upgrade -y
apt install -y nginx mysql-server php8.2 \
    php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring \
    php8.2-curl php8.2-intl php8.2-bcmath php8.2-gd \
    php8.2-zip php8.2-opcache php8.2-redis \
    composer unzip curl wget git fail2ban

info "=== Instalando WP-CLI ==="
curl -sO https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar && mv wp-cli.phar /usr/local/bin/wp

info "=== Configurando MySQL ==="
mysql <<EOF
CREATE DATABASE IF NOT EXISTS consultoria CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'wordpress'@'localhost' IDENTIFIED BY '$(openssl rand -base64 32)';
GRANT ALL PRIVILEGES ON consultoria.* TO 'wordpress'@'localhost';
FLUSH PRIVILEGES;
EOF

DB_PASS=$(mysql -N -e "SELECT authentication_string FROM mysql.user WHERE User='wordpress' LIMIT 1" 2>/dev/null || echo "wordpress")

info "=== Configurando Nginx ==="
rm -f /etc/nginx/sites-enabled/default

cat > /etc/nginx/sites-available/consultoria << 'NGINX_CONF'
server {
    listen 80;
    server_name DOMAIN_PLACEHOLDER www.DOMAIN_PLACEHOLDER;
    root /var/www/html;
    index index.php;

    access_log /var/log/nginx/consultoria-access.log;
    error_log  /var/log/nginx/consultoria-error.log;

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~* \.(jpg|jpeg|png|gif|css|js|ico|svg|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    location ~ /\.ht {
        deny all;
    }

    location = /wp-config.php {
        deny all;
    }
}
NGINX_CONF

sed -i "s/DOMAIN_PLACEHOLDER/$DOMAIN/g" /etc/nginx/sites-available/consultoria
ln -sf /etc/nginx/sites-available/consultoria /etc/nginx/sites-enabled/
nginx -t && systemctl restart nginx

info "=== Configurando PHP ==="
cat > /etc/php/8.2/fpm/conf.d/99-consultoria.ini << 'PHP_INI'
upload_max_filesize = 128M
post_max_size = 128M
max_execution_time = 300
memory_limit = 256M
max_input_vars = 5000
opcache.enable = 1
opcache.memory_consumption = 256
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 60
PHP_INI

systemctl restart php8.2-fpm

info "=== Instalando WordPress ==="
if [ ! -f /var/www/html/wp-config.php ]; then
    wp core download --path=/var/www/html --locale=pt_BR --version=6.4 --allow-root

    cp /var/www/html/wp-config-sample.php /var/www/html/wp-config.php
    chown -R www-data:www-data /var/www/html

    wp config set DB_NAME consultoria --path=/var/www/html --allow-root
    wp config set DB_USER wordpress --path=/var/www/html --allow-root
    wp config set DB_PASSWORD "$DB_PASS" --path=/var/www/html --allow-root
    wp config set DB_HOST localhost --path=/var/www/html --allow-root
    wp config set WP_DEBUG false --path=/var/www/html --allow-root
    wp config set WP_DEBUG_LOG false --path=/var/www/html --allow-root
    wp config set WP_POST_REVISIONS 5 --path=/var/www/html --allow-root
    wp config set FORCE_SSL_ADMIN true --path=/var/www/html --allow-root

    wp core install \
        --path=/var/www/html \
        --url="https://$DOMAIN" \
        --title="Consultoria SaaS" \
        --admin_user="$WP_ADMIN" \
        --admin_password="$(openssl rand -base64 16)" \
        --admin_email="$WP_EMAIL" \
        --locale=pt_BR \
        --skip-email \
        --allow-root

    wp rewrite structure '/%postname%/' --path=/var/www/html --allow-root
fi

info "=== Instalando Plugins ==="
wp plugin install woocommerce --activate --path=/var/www/html --allow-root
wp plugin install redis-cache --activate --path=/var/www/html --allow-root
wp plugin install jwt-authentication-for-wp-rest-api --activate --path=/var/www/html --allow-root || true
wp plugin install really-simple-ssl --path=/var/www/html --allow-root || true

info "=== Configurando Redis ==="
apt install -y redis-server
systemctl enable --now redis-server
wp redis enable --path=/var/www/html --allow-root || true

info "=== Configurando Backup Automático ==="
cat > /etc/cron.d/consultoria-backup << 'CRON'
# Backup MySQL a cada 6h
0 */6 * * * root mysqldump consultoria | gzip > /var/backups/mysql/consultoria_$(date +\%Y\%m\%d_\%H\%M\%S).sql.gz
# Limpar backups com mais de 30 dias
0 3 * * * root find /var/backups/mysql/ -name '*.sql.gz' -mtime +30 -delete
CRON

mkdir -p /var/backups/mysql

info "=== Configurando SSL (Let's Encrypt) ==="
apt install -y certbot python3-certbot-nginx
certbot --nginx -d "$DOMAIN" -d "www.$DOMAIN" --non-interactive --agree-tos -m "$WP_EMAIL" || true

info "=== Configurando Fail2ban ==="
cat > /etc/fail2ban/jail.local << 'FAIL2BAN'
[nginx-http-auth]
enabled = true

[sshd]
enabled = true
bantime = 1h
maxretry = 5
FAIL2BAN
systemctl restart fail2ban

info "=== Configurando Swap ==="
if [ ! -f /swapfile ]; then
    fallocate -l 2G /swapfile
    chmod 600 /swapfile
    mkswap /swapfile
    swapon /swapfile
    echo '/swapfile none swap sw 0 0' >> /etc/fstab
fi

info "=========================================="
info " Servidor configurado com sucesso!"
info "=========================================="
echo ""
warn "Credenciais WordPress:"
wp user list --path=/var/www/html --allow-root 2>/dev/null || true
echo ""
info "Proximos passos:"
echo "  1. Configure o DNS do dominio $DOMAIN para o IP deste servidor"
echo "  2. No GitHub, configure as secrets:"
echo "     - DEPLOY_SSH_KEY: chave privada SSH"
echo "     - DEPLOY_HOST: IP do servidor"
echo "     - DEPLOY_USER: usuario SSH"
echo "  3. No servidor, clone o repo e configure o deploy:"
echo "     git clone https://github.com/jordaoneto-oss/consultoria-saas.git /tmp/deploy"
echo "     rsync -av /tmp/deploy/wp-content/plugins/consultoria-platform/ /var/www/html/wp-content/plugins/"
echo "     rsync -av /tmp/deploy/wp-content/themes/consultoria-theme/ /var/www/html/wp-content/themes/"
echo "     wp plugin activate consultoria-platform --path=/var/www/html"
echo "     wp theme activate consultoria-theme --path=/var/www/html"
echo ""
