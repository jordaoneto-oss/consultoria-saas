<?php

namespace Consultoria\Helpers;

class Logger {

    private static string $logDir = '';
    private static bool $initialized = false;

    public static function init(): void {
        if (self::$initialized) return;
        self::$logDir = WP_CONTENT_DIR . '/uploads/consultoria-logs';
        if (!file_exists(self::$logDir)) {
            wp_mkdir_p(self::$logDir);
        }
        self::$initialized = true;
    }

    public static function debug(string $message, array $context = []): void {
        self::log('DEBUG', $message, $context);
    }

    public static function info(string $message, array $context = []): void {
        self::log('INFO', $message, $context);
    }

    public static function warning(string $message, array $context = []): void {
        self::log('WARNING', $message, $context);
    }

    public static function error(string $message, array $context = []): void {
        self::log('ERROR', $message, $context);
    }

    public static function critical(string $message, array $context = []): void {
        self::log('CRITICAL', $message, $context);
    }

    private static function log(string $level, string $message, array $context = []): void {
        if (defined('WP_DEBUG') && WP_DEBUG === false && $level === 'DEBUG') return;

        self::init();
        $logEntry = [
            'timestamp' => current_time('mysql'),
            'level'     => $level,
            'message'   => $message,
            'context'   => $context,
            'user_id'   => get_current_user_id(),
            'ip'        => $_SERVER['REMOTE_ADDR'] ?? 'cli',
        ];

        $logFile = self::$logDir . '/consultoria-' . date('Y-m-d') . '.log';
        $line = json_encode($logEntry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
        file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);

        if (in_array($level, ['ERROR', 'CRITICAL'])) {
            error_log("[Consultoria Platform] [$level] $message");
        }
    }
}
