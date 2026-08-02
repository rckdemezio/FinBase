<?php

declare(strict_types=1);

use Demezio\Finbase\Core\Http\JsonResponse;
use Demezio\Finbase\Core\Http\ExceptionHandler;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\Routing\Router;
use Demezio\Finbase\Finance\Presentation\Http\Controller\CreateAccountController;
use Demezio\Finbase\Finance\Presentation\Http\Controller\CreditAccountController;
use Demezio\Finbase\Finance\Presentation\Http\Controller\DebitAccountController;
use Demezio\Finbase\Finance\Presentation\Http\Controller\GetAccountController;
use Demezio\Finbase\Finance\Presentation\Http\Controller\ListAccountsController;

require __DIR__.'/../vendor/autoload.php';

/** @var Demezio\Finbase\Core\Contracts\ContainerInterface $container */
$container = require __DIR__.'/../bootstrap/app.php';

$router = new Router();
$exceptionHandler = new ExceptionHandler();

$router->get('/health', static fn (Request $request): JsonResponse => new JsonResponse(['status' => 'ok']));
$router->post('/accounts', $container->make(CreateAccountController::class));
$router->get('/accounts', $container->make(ListAccountsController::class));
$router->get('/accounts/{id}', $container->make(GetAccountController::class));
$router->post('/accounts/{id}/credits', $container->make(CreditAccountController::class));
$router->post('/accounts/{id}/debits', $container->make(DebitAccountController::class));

try {
    $response = $router->dispatch(Request::fromGlobals());
} catch (\Throwable $exception) {
    $response = $exceptionHandler->handle($exception);
}

$response->send();
