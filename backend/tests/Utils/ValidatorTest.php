<?php

declare(strict_types=1);

namespace App\Tests\Utils;

use App\Utils\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function testValidData(): void
    {
        $errors = Validator::validate([
            'name' => 'Иван',
            'phone' => '+7 999 123-45-67',
            'email' => 'ivan@example.com',
            'comment' => 'Привет',
        ], [
            'name' => 'required',
            'phone' => 'required|phone',
            'email' => 'required|email',
            'comment' => 'required',
        ]);

        $this->assertEmpty($errors);
    }

    public function testInvalidEmail(): void
    {
        $errors = Validator::validate([
            'email' => 'not-an-email',
        ], [
            'email' => 'required|email',
        ]);

        $this->assertArrayHasKey('email', $errors);
    }

    public function testInvalidPhone(): void
    {
        $errors = Validator::validate([
            'phone' => '123',
        ], [
            'phone' => 'required|phone',
        ]);

        $this->assertArrayHasKey('phone', $errors);
    }

    public function testRequiredFields(): void
    {
        $errors = Validator::validate([], [
            'name' => 'required',
        ]);

        $this->assertArrayHasKey('name', $errors);
    }
}
