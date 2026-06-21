<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Services\Ai\AiServiceInterface;
use App\Services\MetricsService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AiController
{
    public function __construct(
        private readonly AiServiceInterface $aiService,
        private readonly MetricsService $metricsService
    ) {
    }

    public function suggest(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody() ?? [];
        $text = trim((string)($data['text'] ?? ''));

        if ($text === '') {
            throw new ValidationException('Текст не предоставлен');
        }

        $suggestion = $this->aiService->generateReply($text);
        $this->metricsService->recordAiRequest();

        $response->getBody()->write(json_encode([
            'suggestion' => $suggestion,
            'fallback' => str_contains($suggestion, 'Мы ответим вам позже') || str_contains($suggestion, 'ответим в ближайшее время'),
        ], JSON_UNESCAPED_UNICODE));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
