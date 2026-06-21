<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class RequestLoggerMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $start = microtime(true);
        $response = $handler->handle($request);
        $duration = round((microtime(true) - $start) * 1000, 2);

        $this->logger->info('HTTP запрос', [
            'method' => $request->getMethod(),
            'uri' => $request->getUri()->__toString(),
            'status' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'ip' => $this->getClientIp($request),
            'user_agent' => $request->getHeaderLine('User-Agent'),
        ]);

        return $response;
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
