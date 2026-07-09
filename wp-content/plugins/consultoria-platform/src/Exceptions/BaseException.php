<?php

namespace Consultoria\Exceptions;

class BaseException extends \Exception {
    protected int $statusCode = 400;
    protected array $errors = [];

    public function __construct(string $message = '', int $statusCode = 400, array $errors = []) {
        parent::__construct($message, $statusCode);
        $this->statusCode = $statusCode;
        $this->errors = $errors;
    }

    public function getStatusCode(): int {
        return $this->statusCode;
    }

    public function getErrors(): array {
        return $this->errors;
    }

    public function toResponse(): \WP_REST_Response {
        return \Consultoria\Helpers\Response::error($this->getMessage(), $this->statusCode, $this->errors);
    }
}

class ValidationException extends BaseException {
    public function __construct(array $errors, string $message = 'Erro de validação') {
        parent::__construct($message, 422, $errors);
    }
}

class NotFoundException extends BaseException {
    public function __construct(string $message = 'Recurso não encontrado') {
        parent::__construct($message, 404);
    }
}

class UnauthorizedException extends BaseException {
    public function __construct(string $message = 'Não autorizado') {
        parent::__construct($message, 401);
    }
}

class ForbiddenException extends BaseException {
    public function __construct(string $message = 'Acesso negado') {
        parent::__construct($message, 403);
    }
}
