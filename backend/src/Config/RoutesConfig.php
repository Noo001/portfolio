<?php

declare(strict_types=1);

namespace App\Config;

use App\Controllers\AiController;
use App\Controllers\ContactController;
use App\Controllers\HealthController;
use App\Controllers\MetricsController;
use App\Middleware\CorsMiddleware;
use App\Middleware\RateLimitMiddleware;
use App\Middleware\RequestLoggerMiddleware;
use Slim\App;

class RoutesConfig
{
    public static function register(App $app): void
    {
        $app->add(RequestLoggerMiddleware::class);
        $app->add(new CorsMiddleware());

        $app->get('/api/health', [HealthController::class, 'index']);
        $app->get('/api/metrics', [MetricsController::class, 'index']);

        $app->post('/api/contact', [ContactController::class, 'send'])
            ->add(RateLimitMiddleware::class);

        // Обратная совместимость со старым фронтендом
        $app->post('/api/send-message', [ContactController::class, 'send'])
            ->add(RateLimitMiddleware::class);

        $app->post('/api/ai-suggest', [AiController::class, 'suggest']);
    }
}
