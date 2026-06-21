<?php

declare(strict_types=1);

namespace App\Services\Ai;

use Psr\Log\LoggerInterface;

class StubAiService implements AiServiceInterface
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function analyzeSentiment(string $text): array
    {
        $this->logger->info('Stub AI: анализ тональности', ['text_length' => strlen($text)]);

        $lower = mb_strtolower($text);

        if (str_contains($lower, 'плохо') || str_contains($lower, 'ужасно') || str_contains($lower, 'недоволен')) {
            return ['sentiment' => 'negative', 'confidence' => 0.85];
        }

        if (str_contains($lower, 'хорошо') || str_contains($lower, 'отлично') || str_contains($lower, 'спасибо')) {
            return ['sentiment' => 'positive', 'confidence' => 0.9];
        }

        return ['sentiment' => 'neutral', 'confidence' => 0.7];
    }

    public function generateReply(string $text): string
    {
        $this->logger->info('Stub AI: генерация ответа', ['text_length' => strlen($text)]);

        return 'Спасибо за ваше сообщение! Мы получили его и ответим в ближайшее время.';
    }
}
