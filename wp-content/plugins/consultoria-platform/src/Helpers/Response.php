<?php

namespace Consultoria\Helpers;

class Response {

    public static function success($data = [], int $statusCode = 200, array $headers = []): \WP_REST_Response {
        $response = [
            'success' => true,
            'data'    => $data,
            'timestamp' => current_time('mysql'),
        ];
        return new \WP_REST_Response($response, $statusCode, $headers);
    }

    public static function created($data = []): \WP_REST_Response {
        return self::success($data, 201);
    }

    public static function error(string $message, int $statusCode = 400, array $errors = []): \WP_REST_Response {
        $response = [
            'success' => false,
            'error'   => [
                'code'    => $statusCode,
                'message' => $message,
                'errors'  => $errors,
            ],
            'timestamp' => current_time('mysql'),
        ];
        return new \WP_REST_Response($response, $statusCode);
    }

    public static function notFound(string $message = 'Recurso não encontrado'): \WP_REST_Response {
        return self::error($message, 404);
    }

    public static function unauthorized(string $message = 'Não autorizado'): \WP_REST_Response {
        return self::error($message, 401);
    }

    public static function forbidden(string $message = 'Acesso negado'): \WP_REST_Response {
        return self::error($message, 403);
    }

    public static function validationError(array $errors): \WP_REST_Response {
        return self::error('Erro de validação', 422, $errors);
    }

    public static function paginated(array $items, int $total, int $page, int $perPage): \WP_REST_Response {
        return self::success([
            'items'    => $items,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
            'pages'    => (int) ceil($total / $perPage),
        ]);
    }
}
