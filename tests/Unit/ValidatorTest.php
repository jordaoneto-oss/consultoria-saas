<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase {

    private function validateEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function validateRequired(string $value): bool {
        return trim($value) !== '';
    }

    private function validateMinLength(string $value, int $min): bool {
        return mb_strlen(trim($value)) >= $min;
    }

    private function validateNumeric(mixed $value): bool {
        return is_numeric($value);
    }

    private function validateInArray(mixed $value, array $allowed): bool {
        return in_array($value, $allowed, true);
    }

    public function test_valid_email_passes(): void {
        $this->assertTrue($this->validateEmail('user@example.com'));
    }

    public function test_invalid_email_fails(): void {
        $this->assertFalse($this->validateEmail('not-an-email'));
    }

    public function test_required_field_with_value_passes(): void {
        $this->assertTrue($this->validateRequired('some value'));
    }

    public function test_required_field_empty_fails(): void {
        $this->assertFalse($this->validateRequired(''));
    }

    public function test_min_length_passes(): void {
        $this->assertTrue($this->validateMinLength('hello', 3));
    }

    public function test_min_length_fails(): void {
        $this->assertFalse($this->validateMinLength('ab', 3));
    }

    public function test_numeric_value_passes(): void {
        $this->assertTrue($this->validateNumeric(42));
        $this->assertTrue($this->validateNumeric('150.00'));
    }

    public function test_non_numeric_fails(): void {
        $this->assertFalse($this->validateNumeric('abc'));
    }

    public function test_in_array_passes(): void {
        $this->assertTrue($this->validateInArray('medium', ['low', 'medium', 'high']));
    }

    public function test_in_array_fails(): void {
        $this->assertFalse($this->validateInArray('invalid', ['low', 'medium', 'high']));
    }
}
