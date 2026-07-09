<?php

namespace Consultoria\Middleware;

use Consultoria\Helpers\Logger;

class LoggingMiddleware {

    public static function handle(\WP_REST_Request $request, callable $next): \WP_REST_Response {
        $startTime = microtime(true);
        $route = $request->get_route();
        $method = $request->get_method();
        $userId = get_current_user_id();

        Logger::info("API Request: $method $route", [
            'user_id' => $userId,
            'params'  => $request->get_params(),
        ]);

        try {
            $response = $next($request);
            $duration = (microtime(true) - $startTime) * 1000;

            Logger::debug("API Response: $method $route", [
                'duration_ms' => round($duration, 2),
                'status'      => $response->get_status(),
            ]);

            return $response;
        } catch (\Throwable $e) {
            $duration = (microtime(true) - $startTime) * 1000;
            Logger::error("API Error: $method $route", [
                'duration_ms' => round($duration, 2),
                'message'     => $e->getMessage(),
                'trace'       => $e->getTraceAsString(),
            ]);

            return \Consultoria\Helpers\Response::error(
                'Erro interno do servidor',
                500
            );
        }
    }
}
