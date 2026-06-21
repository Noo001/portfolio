<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exceptions\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Throwable;

class ErrorHandler
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface {
        $statusCode = $this->determineStatusCode($exception);
        $debug = filter_var($_ENV['APP_DEBUG'] ?? 'false', FILTER_VALIDATE_BOOLEAN);

        if ($logErrors) {
            $this->logger->error($exception->getMessage(), [
                'exception' => $exception,
                'uri' => $request->getUri()->__toString(),
                'method' => $request->getMethod(),
            ]);
        }

        $payload = [
            'error' => $this->determineMessage($exception, $debug),
        ];

        if ($debug) {
            $payload['trace'] = $exception->getTrace();
        }

        $responseFactory = new ResponseFactory();
        $response = $responseFactory->createResponse($statusCode);
        $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_UNICODE));

        return $response->withHeader('Content-Type', 'application/json');
    }

    private function determineStatusCode(Throwable $exception): int
    {
        if ($exception instanceof ValidationException) {
            return 400;
        }

        $code = $exception->getCode();
        return $code >= 400 && $code < 600 ? $code : 500;
    }

    private function determineMessage(Throwable $exception, bool $debug): string
    {
        if ($exception instanceof ValidationException) {
            return $exception->getMessage();
        }

        if ($debug) {
            return $exception->getMessage();
        }

        $statusCode = $this->determineStatusCode($exception);
        return $statusCode >= 500
            ? 'Внутренняя ошибка сервера. Попробуйте позже.'
            : $exception->getMessage();
    }
}
