<?php

declare(strict_types=1);

use Demezio\Finbase\Core\Http\ExceptionHandler;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\Routing\Router;

require __DIR__.'/../vendor/autoload.php';

/** @var Demezio\Finbase\Core\Contracts\ContainerInterface $container */
$container = require __DIR__.'/../bootstrap/app.php';

$router = new Router();
$exceptionHandler = new ExceptionHandler();

/** @var callable(Router, Demezio\Finbase\Core\Contracts\ContainerInterface): void $registerApiRoutes */
$registerApiRoutes = require __DIR__.'/../routes/api.php';
$registerApiRoutes($router, $container);

/** @var callable(Router, Demezio\Finbase\Core\Contracts\ContainerInterface): void $registerWebRoutes */
$registerWebRoutes = require __DIR__.'/../routes/web.php';
$registerWebRoutes($router, $container);

try {
    $response = $router->dispatch(Request::fromGlobals());
} catch (\Throwable $exception) {
    $response = $exceptionHandler->handle($exception);
}

$response->send();
