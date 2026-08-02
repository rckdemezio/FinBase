<?php

declare(strict_types=1);

use Demezio\Finbase\Core\Http\JsonResponse;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\Routing\Router;
use Demezio\Finbase\Finance\Presentation\Http\Controller\CreateAccountController;

require __DIR__.'/../vendor/autoload.php';

/** @var Demezio\Finbase\Core\Contracts\ContainerInterface $container */
$container = require __DIR__.'/../bootstrap/app.php';

$router = new Router();

$router->get('/health', static fn (Request $request): JsonResponse => new JsonResponse(['status' => 'ok']));
$router->post('/accounts', $container->make(CreateAccountController::class));

$router->dispatch(Request::fromGlobals())->send();
