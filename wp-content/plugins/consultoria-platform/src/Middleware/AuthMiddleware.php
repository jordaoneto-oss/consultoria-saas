<?php

namespace Consultoria\Middleware;

class AuthMiddleware {

    public static function authenticate(): ?object {
        $token = self::getBearerToken();
        if (!$token) return null;

        $payload = self::validateToken($token);
        if (!$payload || !isset($payload->user_id)) return null;

        $user = get_user_by('ID', $payload->user_id);
        if (!$user) return null;

        wp_set_current_user($user->ID);
        return $user;
    }

    public static function generateToken(int $userId): string {
        $issuedAt = time();
        $expiresAt = $issuedAt + (HOUR_IN_SECONDS * 24); // 24 horas

        $payload = [
            'iss'      => get_site_url(),
            'iat'      => $issuedAt,
            'exp'      => $expiresAt,
            'user_id'  => $userId,
            'role'     => self::getUserRole($userId),
        ];

        return self::encodeJWT($payload);
    }

    public static function generateRefreshToken(int $userId): string {
        $token = bin2hex(random_bytes(32));
        update_user_meta($userId, 'cp_refresh_token', $token);
        update_user_meta($userId, 'cp_refresh_token_expires', time() + (DAY_IN_SECONDS * 30));
        return $token;
    }

    public static function validateRefreshToken(string $token): ?int {
        global $wpdb;

        $userId = $wpdb->get_var($wpdb->prepare(
            "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = 'cp_refresh_token' AND meta_value = %s",
            $token
        ));

        if (!$userId) return null;

        $expires = get_user_meta($userId, 'cp_refresh_token_expires', true);
        if (!$expires || $expires < time()) return null;

        return (int) $userId;
    }

    private static function getBearerToken(): ?string {
        $headers = self::getAuthorizationHeader();
        if (!$headers) return null;

        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private static function getAuthorizationHeader(): ?string {
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return $_SERVER['HTTP_AUTHORIZATION'];
        }
        if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            return $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (isset($headers['Authorization'])) {
                return $headers['Authorization'];
            }
        }
        return null;
    }

    private static function validateToken(string $token): ?object {
        $payload = self::decodeJWT($token);
        if (!$payload) return null;

        // Verificar expiração
        if (isset($payload->exp) && $payload->exp < time()) {
            return null;
        }

        return $payload;
    }

    private static function encodeJWT(array $payload): string {
        $secret = self::getSecretKey();
        $header = self::base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = self::base64UrlEncode(json_encode($payload));
        $signature = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", $secret, true)
        );

        return "$header.$payload.$signature";
    }

    private static function decodeJWT(string $token): ?object {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;

        [$header, $payload, $signature] = $parts;

        $secret = self::getSecretKey();
        $expectedSignature = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", $secret, true)
        );

        if (!hash_equals($expectedSignature, $signature)) {
            return null;
        }

        return json_decode(self::base64UrlDecode($payload));
    }

    private static function getSecretKey(): string {
        $key = get_option('cp_jwt_secret');
        if (!$key) {
            $key = wp_generate_password(64, true, true);
            update_option('cp_jwt_secret', $key);
        }
        return $key;
    }

    private static function base64UrlEncode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    private static function getUserRole(int $userId): string {
        $user = get_userdata($userId);
        if (!$user) return '';

        $roles = $user->roles;
        if (in_array('administrator', $roles)) return 'admin';
        if (in_array('cp_consultant', $roles)) return 'consultant';
        if (in_array('cp_client', $roles)) return 'client';
        if (in_array('cp_support', $roles)) return 'support';

        return 'client';
    }
}
