<?php

declare(strict_types=1);

namespace App\Services\Ai;

interface AiServiceInterface
{
    /**
     * Анализ тональности текста.
     *
     * @return array<string, mixed>
     */
    public function analyzeSentiment(string $text): array;

    /**
     * Генерация ответа на обращение.
     */
    public function generateReply(string $text): string;
}
