<?php

declare(strict_types=1);

namespace App\Utils;

class Validator
{
    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $rules
     * @return array<string, string>
     */
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            $ruleParts = explode('|', $rule);

            foreach ($ruleParts as $part) {
                if ($part === 'required' && self::isEmpty($value)) {
                    $errors[$field] = "Поле {$field} обязательно для заполнения";
                    break;
                }

                if ($part === 'email' && !self::isEmpty($value) && !filter_var((string)$value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "Поле {$field} должно содержать корректный email";
                    break;
                }

                if ($part === 'phone' && !self::isEmpty($value) && !self::isValidPhone((string)$value)) {
                    $errors[$field] = "Поле {$field} должно содержать корректный номер телефона";
                    break;
                }
            }
        }

        return $errors;
    }

    private static function isEmpty(mixed $value): bool
    {
        return $value === null || $value === '' || (is_string($value) && trim($value) === '');
    }

    private static function isValidPhone(string $phone): bool
    {
        $digits = preg_replace('/\D/', '', $phone);
        return !empty($digits) && strlen($digits) >= 7 && strlen($digits) <= 15;
    }
}
