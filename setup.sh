#!/usr/bin/env bash
set -euo pipefail

# ============================================================
# Consultoria SaaS - Setup Script
# ============================================================
# Uso: bash setup.sh
#
# Instala dependências, configura banco, WordPress e plugin.
# ============================================================

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

info()  { echo -e "${GREEN}[INFO]${NC} $1"; }
warn()  { echo -e "${YELLOW}[WARN]${NC} $1"; }
error() { echo -e "${RED}[ERROR]${NC} $1"; }

info "=== Consultoria SaaS - Setup ==="

# --------------------------------------------------
# 1. Verificar Homebrew
# --------------------------------------------------
if ! command -v brew &>/dev/null; then
    error "Homebrew não encontrado. Instale em https://brew.sh"
    exit 1
fi

# --------------------------------------------------
# 2. Instalar dependências
# --------------------------------------------------
info "Instalando dependências via Homebrew..."

if ! command -v mysql &>/dev/null; then
    brew install mysql@8.0
fi

if ! command -v php &>/dev/null || ! php -v | grep -q "PHP 8"; then
    brew install php@8.2
fi

if ! command -v composer &>/dev/null; then
    brew install composer
fi

if ! command -v wp &>/dev/null; then
    brew install wp-cli
fi

if ! command -v nginx &>/dev/null; then
    brew install nginx
fi

export PATH="/usr/local/opt/mysql@8.0/bin:$PATH"
export PATH="/usr/local/opt/php@8.2/bin:$PATH"

info "Dependências instaladas."

# --------------------------------------------------
# 3. Iniciar serviços
# --------------------------------------------------
info "Iniciando serviços..."
brew services start mysql@8.0 2>/dev/null || warn "mysql@8.0 já estava rodando"
brew services start php@8.2   2>/dev/null || warn "php@8.2 já estava rodando"
brew services start nginx     2>/dev/null || warn "nginx já estava rodando"

sleep 3

# --------------------------------------------------
# 4. Criar banco de dados
# --------------------------------------------------
info "Configurando banco de dados..."
MYSQL_ADMIN="mysql -u root"

# Criar banco se não existir
$MYSQL_ADMIN -e "CREATE DATABASE IF NOT EXISTS consultoria CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
$MYSQL_ADMIN -e "CREATE USER IF NOT EXISTS 'wordpress'@'localhost' IDENTIFIED BY 'wordpress';"
$MYSQL_ADMIN -e "GRANT ALL PRIVILEGES ON consultoria.* TO 'wordpress'@'localhost';"
$MYSQL_ADMIN -e "FLUSH PRIVILEGES;"

# Importar schema
info "Importando schema SQL..."
mysql -u wordpress -pwordpress consultoria < docs/database/SQL_COMPLETE.sql

info "Banco configurado."

# --------------------------------------------------
# 5. Instalar WordPress
# --------------------------------------------------
WP_DIR="wp"
WP_URL="http://localhost:8080"

if [ ! -f "$WP_DIR/wp-config.php" ]; then
    info "Baixando WordPress..."
    mkdir -p "$WP_DIR"
    wp core download --path="$WP_DIR" --locale=pt_BR --version=6.4

    info "Configurando wp-config.php..."
    cp wp-config-sample.php "$WP_DIR/wp-config.php"
    sed -i '' "s/DB_NAME.*$/define('DB_NAME', 'consultoria');/" "$WP_DIR/wp-config.php"
    sed -i '' "s/DB_USER.*$/define('DB_USER', 'wordpress');/" "$WP_DIR/wp-config.php"
    sed -i '' "s/DB_PASSWORD.*$/define('DB_PASSWORD', 'wordpress');/" "$WP_DIR/wp-config.php"
    sed -i '' "s/DB_HOST.*$/define('DB_HOST', 'localhost');/" "$WP_DIR/wp-config.php"

    wp core install \
        --path="$WP_DIR" \
        --url="$WP_URL" \
        --title="Consultoria SaaS" \
        --admin_user="admin" \
        --admin_password="admin123" \
        --admin_email="admin@consultoriasaas.com.br" \
        --locale=pt_BR \
        --skip-email

    info "WordPress instalado."
else
    info "WordPress já configurado."
fi

# --------------------------------------------------
# 6. Instalar plugins
# --------------------------------------------------
info "Instalando plugins necessários..."
wp plugin install woocommerce --activate --path="$WP_DIR" 2>/dev/null || warn "WooCommerce já instalado"

# Instalar plugins auxiliares
wp plugin install redis-cache --activate --path="$WP_DIR" 2>/dev/null || true
wp plugin install jwt-authentication-for-wp-rest-api --activate --path="$WP_DIR" 2>/dev/null || true

# Link simbólico do plugin consultoria-platform
PLUGIN_SRC="$PWD/wp-content/plugins/consultoria-platform"
PLUGIN_DST="$PWD/$WP_DIR/wp-content/plugins/consultoria-platform"
if [ ! -L "$PLUGIN_DST" ] && [ ! -d "$PLUGIN_DST" ]; then
    ln -sf "$PLUGIN_SRC" "$PLUGIN_DST"
    info "Plugin consultoria-platform vinculado."
fi

wp plugin activate consultoria-platform --path="$WP_DIR"

# --------------------------------------------------
# 7. Instalar tema
# --------------------------------------------------
THEME_SRC="$PWD/wp-content/themes/consultoria-theme"
THEME_DST="$PWD/$WP_DIR/wp-content/themes/consultoria-theme"
if [ ! -L "$THEME_DST" ] && [ ! -d "$THEME_DST" ]; then
    ln -sf "$THEME_SRC" "$THEME_DST"
    info "Tema consultoria-theme vinculado."
fi

wp theme activate consultoria-theme --path="$WP_DIR"

# --------------------------------------------------
# 8. Configurar WooCommerce
# --------------------------------------------------
info "Configurando WooCommerce..."
wp option update woocommerce_onboarding_opt_in no --path="$WP_DIR" 2>/dev/null || true
wp option update woocommerce_store_address "Av. Principal" --path="$WP_DIR" 2>/dev/null || true
wp option update woocommerce_store_city "São Paulo" --path="$WP_DIR" 2>/dev/null || true
wp option update woocommerce_default_country "BR" --path="$WP_DIR" 2>/dev/null || true
wp option update woocommerce_currency "BRL" --path="$WP_DIR" 2>/dev/null || true
wp option update woocommerce_enable_guest_checkout "yes" --path="$WP_DIR" 2>/dev/null || true
wp option update woocommerce_enable_signup_and_login_from_checkout "yes" --path="$WP_DIR" 2>/dev/null || true

# --------------------------------------------------
# 9. Configurar nginx
# --------------------------------------------------
NGINX_CONF="/usr/local/etc/nginx/servers/consultoria.conf"
if [ ! -f "$NGINX_CONF" ]; then
    info "Configurando nginx..."
    sudo mkdir -p /usr/local/etc/nginx/servers 2>/dev/null || true
    cat > "$NGINX_CONF" << 'EOF'
server {
    listen 8080;
    server_name localhost;
    root __WP_DIR__;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }

    location ~* \.(jpg|jpeg|png|gif|css|js|ico|svg|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
EOF
    sed -i '' "s|__WP_DIR__|$PWD/$WP_DIR|" "$NGINX_CONF"
    brew services restart nginx
    info "nginx configurado."
fi

# --------------------------------------------------
# 10. Final
# --------------------------------------------------
echo ""
info "=========================================="
info " Setup concluído!"
info "=========================================="
echo ""
info "WordPress : http://localhost:8080"
info "Admin     : http://localhost:8080/wp-admin"
info "Usuário   : admin"
info "Senha     : admin123"
echo ""
info "Plugin    : Consultoria Platform ativo"
info "Tema      : Consultoria Theme ativo"
echo ""
warn "IMPORTANTE: Configure as chaves Stripe no .env"
warn "e rode: wp plugin activate jwt-authentication-for-wp-rest-api"
echo ""
