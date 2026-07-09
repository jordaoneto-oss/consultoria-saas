<?php

define('DB_NAME',     getenv('DB_NAME') ?: 'consultoria');
define('DB_USER',     getenv('DB_USER') ?: 'wordpress');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: 'wordpress');
define('DB_HOST',     getenv('DB_HOST') ?: 'mysql');
define('DB_CHARSET',  'utf8mb4');
define('DB_COLLATE',  'utf8mb4_unicode_ci');

$table_prefix = getenv('DB_PREFIX') ?: 'wp_';

define('WP_HOME',    getenv('WP_HOME') ?: 'http://localhost:8080');
define('WP_SITEURL', getenv('WP_SITEURL') ?: 'http://localhost:8080/wp');

define('WP_DEBUG',         filter_var(getenv('WP_DEBUG'), FILTER_VALIDATE_BOOLEAN) ?: false);
define('WP_DEBUG_LOG',     filter_var(getenv('WP_DEBUG_LOG'), FILTER_VALIDATE_BOOLEAN) ?: false);
define('WP_DEBUG_DISPLAY', filter_var(getenv('WP_DEBUG_DISPLAY'), FILTER_VALIDATE_BOOLEAN) ?: false);

define('WP_REDIS_HOST', getenv('REDIS_HOST') ?: 'redis');
define('WP_REDIS_PORT', (int) (getenv('REDIS_PORT') ?: 6379);

define('JWT_AUTH_SECRET_KEY', getenv('JWT_SECRET_KEY') ?: 'dev-secret-key-change-in-production');
define('JWT_AUTH_CORS_ENABLE', true);

define('DISABLE_WP_CRON', false);
define('WP_POST_REVISIONS', 5);
define('MEDIA_TRASH', true);
define('EMPTY_TRASH_DAYS', 30);
define('FORCE_SSL_ADMIN', false);

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

require_once ABSPATH . 'wp-settings.php';
