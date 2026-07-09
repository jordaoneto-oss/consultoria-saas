<?php

namespace Consultoria\Helpers;

class Functions {

    public static function generateSlug(string $string): string {
        return sanitize_title($string);
    }

    public static function generateCode(int $length = 8): string {
        return substr(bin2hex(random_bytes($length)), 0, $length);
    }

    public static function formatCurrency(float $value, string $currency = 'BRL'): string {
        if ($currency === 'BRL') {
            return 'R$ ' . number_format($value, 2, ',', '.');
        }
        return '$ ' . number_format($value, 2, '.', ',');
    }

    public static function formatDate(string $date, string $format = 'd/m/Y H:i'): string {
        return date_i18n($format, strtotime($date));
    }

    public static function calculatePercentage(float $value, float $percentage): float {
        return round($value * ($percentage / 100), 2);
    }

    public static function sanitizePhone(string $phone): string {
        return preg_replace('/\D/', '', $phone);
    }

    public static function maskEmail(string $email): string {
        $parts = explode('@', $email);
        $name = $parts[0];
        $len = strlen($name);
        $masked = substr($name, 0, 2) . str_repeat('*', max(0, $len - 4)) . substr($name, -2);
        return $masked . '@' . $parts[1];
    }

    public static function timeAgo(string $datetime): string {
        $timestamp = strtotime($datetime);
        $diff = current_time('timestamp') - $timestamp;

        if ($diff < 60) return 'agora mesmo';
        if ($diff < 3600) return floor($diff / 60) . ' min atrás';
        if ($diff < 86400) return floor($diff / 3600) . 'h atrás';
        if ($diff < 604800) return floor($diff / 86400) . ' dias atrás';
        return self::formatDate($datetime, 'd/m/Y');
    }

    public static function truncate(string $text, int $limit = 100): string {
        if (mb_strlen($text) <= $limit) return $text;
        return mb_substr($text, 0, $limit) . '...';
    }
}
