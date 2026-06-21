<?php

declare(strict_types=1);

use App\Config\ContainerConfig;
use App\Config\RoutesConfig;
use App\Database\Migrator;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$builder = new ContainerBuilder();
$builder->addDefinitions(ContainerConfig::getDefinitions());
$container = $builder->build();

// Запускаем миграции базы данных
$container->get(Migrator::class)->migrate();

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

RoutesConfig::register($app);

$errorMiddleware = $app->addErrorMiddleware(filter_var($_ENV['APP_DEBUG'] ?? 'false', FILTER_VALIDATE_BOOLEAN), true, true);
$errorMiddleware->setDefaultErrorHandler($container->get(App\Middleware\ErrorHandler::class));

$app->run();
