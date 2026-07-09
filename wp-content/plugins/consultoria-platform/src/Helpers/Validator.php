<?php

namespace Consultoria\Helpers;

class Validator {

    private array $errors = [];

    public function validate(array $data, array $rules): bool {
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $fieldRules = is_array($fieldRules) ? $fieldRules : explode('|', $fieldRules);

            foreach ($fieldRules as $rule) {
                $params = [];
                if (strpos($rule, ':') !== false) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }

                $methodName = 'rule' . ucfirst($rule);
                if (method_exists($this, $methodName)) {
                    $this->$methodName($field, $value, $params);
                }
            }
        }

        return empty($this->errors);
    }

    public function getErrors(): array {
        return $this->errors;
    }

    public function getFirstError(): ?string {
        return !empty($this->errors) ? $this->errors[array_key_first($this->errors)][0] : null;
    }

    private function addError(string $field, string $message): void {
        $this->errors[$field][] = $message;
    }

    private function ruleRequired(string $field, $value, array $params = []): void {
        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            $this->addError($field, "O campo $field é obrigatório.");
        }
    }

    private function ruleEmail(string $field, $value, array $params = []): void {
        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "O campo $field deve ser um email válido.");
        }
    }

    private function ruleMin(string $field, $value, array $params = []): void {
        $min = (int) ($params[0] ?? 0);
        if (is_string($value) && strlen($value) < $min) {
            $this->addError($field, "O campo $field deve ter no mínimo $min caracteres.");
        }
        if (is_numeric($value) && $value < $min) {
            $this->addError($field, "O campo $field deve ser no mínimo $min.");
        }
    }

    private function ruleMax(string $field, $value, array $params = []): void {
        $max = (int) ($params[0] ?? 0);
        if (is_string($value) && strlen($value) > $max) {
            $this->addError($field, "O campo $field deve ter no máximo $max caracteres.");
        }
        if (is_numeric($value) && $value > $max) {
            $this->addError($field, "O campo $field deve ser no máximo $max.");
        }
    }

    private function ruleNumeric(string $field, $value, array $params = []): void {
        if ($value !== null && $value !== '' && !is_numeric($value)) {
            $this->addError($field, "O campo $field deve ser numérico.");
        }
    }

    private function ruleInteger(string $field, $value, array $params = []): void {
        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_INT)) {
            $this->addError($field, "O campo $field deve ser um número inteiro.");
        }
    }

    private function ruleUrl(string $field, $value, array $params = []): void {
        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_URL)) {
            $this->addError($field, "O campo $field deve ser uma URL válida.");
        }
    }

    private function ruleIn(string $field, $value, array $params = []): void {
        if ($value !== null && $value !== '' && !in_array($value, $params)) {
            $this->addError($field, "O campo $field deve ser um dos valores: " . implode(', ', $params) . ".");
        }
    }

    private function ruleCpf(string $field, $value, array $params = []): void {
        if ($value !== null && $value !== '' && !$this->validateCpf($value)) {
            $this->addError($field, "O campo $field deve ser um CPF válido.");
        }
    }

    private function ruleCnpj(string $field, $value, array $params = []): void {
        if ($value !== null && $value !== '' && !$this->validateCnpj($value)) {
            $this->addError($field, "O campo $field deve ser um CNPJ válido.");
        }
    }

    private function rulePhone(string $field, $value, array $params = []): void {
        if ($value !== null && $value !== '') {
            $cleaned = preg_replace('/\D/', '', $value);
            if (strlen($cleaned) < 10 || strlen($cleaned) > 11) {
                $this->addError($field, "O campo $field deve ser um telefone válido.");
            }
        }
    }

    private function ruleDate(string $field, $value, array $params = []): void {
        if ($value !== null && $value !== '' && !strtotime($value)) {
            $this->addError($field, "O campo $field deve ser uma data válida.");
        }
    }

    private function ruleBoolean(string $field, $value, array $params = []): void {
        if ($value !== null && !in_array($value, [true, false, 0, 1, '0', '1'], true)) {
            $this->addError($field, "O campo $field deve ser um booleano.");
        }
    }

    private function ruleArray(string $field, $value, array $params = []): void {
        if ($value !== null && !is_array($value)) {
            $this->addError($field, "O campo $field deve ser um array.");
        }
    }

    private function validateCpf(?string $cpf): bool {
        $cpf = preg_replace('/\D/', '', $cpf);
        if (strlen($cpf) !== 11) return false;
        if (preg_match('/^(\d)\1{10}$/', $cpf)) return false;

        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) return false;
        }
        return true;
    }

    private function validateCnpj(?string $cnpj): bool {
        $cnpj = preg_replace('/\D/', '', $cnpj);
        if (strlen($cnpj) !== 14) return false;

        // Validação de CNPJ
        $cnpj = str_pad($cnpj, 14, '0', STR_PAD_LEFT);
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) return false;

        $bases = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        for ($i = 12; $i <= 13; $i++) {
            $soma = 0;
            foreach ($bases as $pos => $base) {
                if ($pos >= $i) break;
                $soma += $cnpj[$pos] * $base;
            }
            $resto = ($soma * 10) % 11;
            if ($resto == 10) $resto = 0;
            if ($cnpj[$i] != $resto) return false;
            $bases = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
            array_shift($bases);
        }

        return true;
    }
}
