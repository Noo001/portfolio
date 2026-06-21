<?php

declare(strict_types=1);

namespace App\Services\Ai;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class OpenAiService implements AiServiceInterface
{
    private readonly Client $client;

    public function __construct(
        private readonly string $apiKey,
        private readonly string $model,
        private readonly LoggerInterface $logger
    ) {
        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'timeout' => 15,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function analyzeSentiment(string $text): array
    {
        $this->logger->info('OpenAI: анализ тональности');

        try {
            $response = $this->client->post('chat/completions', [
                'json' => [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Ты анализатор тональности. Ответь только JSON: {"sentiment": "positive|neutral|negative", "confidence": 0.0-1.0}.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $text,
                        ],
                    ],
                    'temperature' => 0.2,
                ],
            ]);

            $body = json_decode((string)$response->getBody(), true);
            $content = $body['choices'][0]['message']['content'] ?? '{}';

            $result = json_decode($content, true);

            return [
                'sentiment' => $result['sentiment'] ?? 'neutral',
                'confidence' => (float)($result['confidence'] ?? 0.5),
            ];
        } catch (GuzzleException $e) {
            $this->logger->error('OpenAI sentiment error: ' . $e->getMessage());
            return ['sentiment' => 'neutral', 'confidence' => 0.0, 'fallback' => true];
        }
    }

    public function generateReply(string $text): string
    {
        $this->logger->info('OpenAI: генерация ответа');

        try {
            $response = $this->client->post('chat/completions', [
                'json' => [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Ты помощник сайта портфолио. Напиши вежливый краткий ответ на сообщение пользователя на русском языке.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $text,
                        ],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 200,
                ],
            ]);

            $body = json_decode((string)$response->getBody(), true);

            return $body['choices'][0]['message']['content'] ?? 'Спасибо за ваше сообщение!';
        } catch (GuzzleException $e) {
            $this->logger->error('OpenAI reply error: ' . $e->getMessage());
            return 'Спасибо за ваше сообщение! Мы ответим вам позже.';
        }
    }
}
