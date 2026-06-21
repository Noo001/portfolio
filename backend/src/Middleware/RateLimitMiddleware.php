<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\RateLimitService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class RateLimitMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly RateLimitService $rateLimitService)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $clientIp = $this->getClientIp($request);
        $identifier = $clientIp . ':' . $request->getUri()->getPath();

        if (!$this->rateLimitService->allow($identifier)) {
            $response = new Response();
            $response->getBody()->write(json_encode([
                'error' => 'Слишком много запросов. Попробуйте позже.',
            ]));

            return $response
                ->withStatus(429)
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('X-RateLimit-Limit', (string)$this->rateLimitService->getMaxRequests())
                ->withHeader('X-RateLimit-Remaining', '0');
        }

        $response = $handler->handle($request);

        return $response
            ->withHeader('X-RateLimit-Limit', (string)$this->rateLimitService->getMaxRequests())
            ->withHeader('X-RateLimit-Remaining', (string)$this->rateLimitService->getRemaining($identifier));
    }

    private function getClientIp(ServerRequestInterface $request): string
    {
        $headers = $request->getHeaders();

        foreach (['X-Forwarded-For', 'X-Real-Ip', 'Remote-Addr'] as $header) {
            if (!empty($headers[$header])) {
                $ips = explode(',', (string)$headers[$header][0]);
                return trim($ips[0]);
            }
        }

        $serverParams = $request->getServerParams();
        return $serverParams['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}
