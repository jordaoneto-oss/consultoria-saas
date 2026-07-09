# Consultoria SaaS — Guia Rápido

## Opção 1: GitHub Codespaces (recomendado para dev)

[![Open in GitHub Codespaces](https://github.com/codespaces/badge.svg)](https://codespaces.new)

```bash
# No terminal do Codespaces:
bash setup.sh
```

## Opção 2: macOS (local)

```bash
# 1. Instalar dependências
brew install mysql@8.0 php@8.2 wp-cli composer nginx

# 2. Iniciar serviços
brew services start mysql@8.0
brew services start php@8.2
brew services start nginx

# 3. Setup automatizado
bash setup.sh
```

## Opção 3: Ubuntu/Debian (servidor)

```bash
# 1. LAMP stack
sudo apt update && sudo apt install -y nginx mysql-server php8.2 \
  php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring \
  php8.2-curl php8.2-intl php8.2-bcmath composer wp-cli

# 2. Banco de dados
sudo mysql -e "CREATE DATABASE IF NOT EXISTS consultoria CHARACTER SET utf8mb4"
sudo mysql consultoria < docs/database/SQL_COMPLETE.sql

# 3. Configurar WordPress
sudo ln -sf "$PWD/wp-content/plugins/consultoria-platform" /var/www/html/wp-content/plugins/
sudo ln -sf "$PWD/wp-content/themes/consultoria-theme" /var/www/html/wp-content/themes/
# Configurar wp-config.php e nginx manualmente
```

## Opção 4: Docker (qualquer plataforma)

```bash
cp .env.example .env
docker compose up -d
# Acessar http://localhost:8080, instalar WP + WooCommerce, ativar plugin
```

---

## Checklist pós-setup

- [ ] WordPress rodando em http://localhost:8080
- [ ] WooCommerce ativado e configurado (moeda BRL)
- [ ] Plugin `consultoria-platform` ativado
- [ ] Tema `consultoria-theme` ativado
- [ ] Redis cache configurado (`wp plugin activate redis-cache`)
- [ ] JWT Authentication ativado
- [ ] Chaves Stripe configuradas no .env
- [ ] Chave Daily.co configurada (se for usar videoconferência)
- [ ] Cron jobs configurados: `cp_daily_cron`, `cp_hourly_cron`
- [ ] Permalinks configurados como "Post name"

## Testar instalação

```bash
# Verificar plugin
wp plugin status consultoria-platform

# Verificar tabelas
wp db query "SELECT COUNT(*) as total_tables FROM information_schema.tables WHERE table_schema='consultoria'" 2>/dev/null
# Deve retornar 37 tabelas

# Verificar seeds
wp db query "SELECT COUNT(*) FROM wp_cp_service_plans"
# Deve retornar 4 planos (Bronze, Prata, Ouro, Enterprise)
```

## Credenciais padrão

- **Admin:** `admin` / `admin123`
- **MySQL:** `wordpress` / `wordpress` (banco: `consultoria`)
- **MailHog:** http://localhost:8025 (emails de desenvolvimento)
- **phpMyAdmin:** http://localhost:8081
