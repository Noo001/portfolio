<?php

declare(strict_types=1);

namespace App\Config;

use App\Database\Database;
use App\Database\Migrator;
use App\Middleware\ErrorHandler;
use App\Middleware\RequestLoggerMiddleware;
use App\Repositories\ContactRepositoryInterface;
use App\Repositories\MetricsRepositoryInterface;
use App\Repositories\RateLimitRepositoryInterface;
use App\Repositories\SQLiteContactRepository;
use App\Repositories\SQLiteMetricsRepository;
use App\Repositories\SQLiteRateLimitRepository;
use App\Services\Ai\AiServiceInterface;
use App\Services\Ai\OpenAiService;
use App\Services\Ai\StubAiService;
use App\Services\ContactService;
use App\Services\EmailService;
use App\Services\MetricsService;
use App\Services\RateLimitService;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class ContainerConfig
{
    public static function getDefinitions(): array
    {
        return [
            Database::class => function (): Database {
                $path = $_ENV['DATABASE_PATH'] ?? 'data/portfolio.sqlite';
                if (!str_starts_with($path, DIRECTORY_SEPARATOR) && !preg_match('/^[A-Za-z]:\\\\/', $path)) {
                    $path = __DIR__ . '/../../' . $path;
                }
                return new Database($path);
            },

            Migrator::class => function (ContainerInterface $container): Migrator {
                return new Migrator($container->get(Database::class));
            },

            LoggerInterface::class => function (): LoggerInterface {
                $logPath = __DIR__ . '/../../logs/app.log';
                $logger = new Logger('portfolio');
                $logger->pushHandler(new StreamHandler($logPath, Level::Debug));
                return $logger;
            },

            RateLimitRepositoryInterface::class => function (ContainerInterface $container): RateLimitRepositoryInterface {
                return new SQLiteRateLimitRepository($container->get(Database::class));
            },

            MetricsRepositoryInterface::class => function (ContainerInterface $container): MetricsRepositoryInterface {
                return new SQLiteMetricsRepository($container->get(Database::class));
            },

            ContactRepositoryInterface::class => function (ContainerInterface $container): ContactRepositoryInterface {
                return new SQLiteContactRepository($container->get(Database::class));
            },

            AiServiceInterface::class => function (ContainerInterface $container): AiServiceInterface {
                $provider = $_ENV['AI_PROVIDER'] ?? 'stub';
                $apiKey = $_ENV['AI_API_KEY'] ?? '';
                $model = $_ENV['AI_MODEL'] ?? 'gpt-4o-mini';
                $logger = $container->get(LoggerInterface::class);

                if ($provider === 'openai' && !empty($apiKey)) {
                    return new OpenAiService($apiKey, $model, $logger);
                }

                return new StubAiService($logger);
            },

            EmailService::class => function (ContainerInterface $container): EmailService {
                return new EmailService(
                    host: $_ENV['SMTP_HOST'] ?? '',
                    port: (int)($_ENV['SMTP_PORT'] ?? 587),
                    username: $_ENV['SMTP_USERNAME'] ?? '',
                    password: $_ENV['SMTP_PASSWORD'] ?? '',
                    fromEmail: $_ENV['SMTP_FROM_EMAIL'] ?? '',
                    fromName: $_ENV['SMTP_FROM_NAME'] ?? 'Portfolio Site',
                    ownerEmail: $_ENV['OWNER_EMAIL'] ?? '',
                    logger: $container->get(LoggerInterface::class)
                );
            },

            ContactService::class => function (ContainerInterface $container): ContactService {
                return new ContactService(
                    emailService: $container->get(EmailService::class),
                    aiService: $container->get(AiServiceInterface::class),
                    metricsService: $container->get(MetricsService::class),
                    contactRepository: $container->get(ContactRepositoryInterface::class),
                    logger: $container->get(LoggerInterface::class)
                );
            },

            RateLimitService::class => function (ContainerInterface $container): RateLimitService {
                return new RateLimitService(
                    repository: $container->get(RateLimitRepositoryInterface::class),
                    maxRequests: (int)($_ENV['RATE_LIMIT_MAX'] ?? 5),
                    windowSeconds: (int)($_ENV['RATE_LIMIT_WINDOW'] ?? 60)
                );
            },

            MetricsService::class => function (ContainerInterface $container): MetricsService {
                return new MetricsService(
                    repository: $container->get(MetricsRepositoryInterface::class)
                );
            },

            ErrorHandler::class => function (ContainerInterface $container): ErrorHandler {
                return new ErrorHandler($container->get(LoggerInterface::class));
            },

            RequestLoggerMiddleware::class => function (ContainerInterface $container): RequestLoggerMiddleware {
                return new RequestLoggerMiddleware($container->get(LoggerInterface::class));
            },
        ];
    }
}
