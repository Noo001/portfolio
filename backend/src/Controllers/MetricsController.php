<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\MetricsService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MetricsController
{
    public function __construct(private readonly MetricsService $metricsService)
    {
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write(json_encode([
            'metrics' => $this->metricsService->getMetrics(),
            'timestamp' => date('c'),
        ], JSON_UNESCAPED_UNICODE));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
