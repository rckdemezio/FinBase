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
use Demezio\Finbase\Finance\Presentation\Web\Controller\ListAccountsPageController;
use Demezio\Finbase\Finance\Presentation\Web\Controller\CreateAccountPageController;
use Demezio\Finbase\Finance\Presentation\Web\Controller\GetAccountPageController;
use Demezio\Finbase\Finance\Presentation\Web\Controller\StoreAccountController;

require __DIR__.'/../vendor/autoload.php';

/** @var Demezio\Finbase\Core\Contracts\ContainerInterface $container */
$container = require __DIR__.'/../bootstrap/app.php';

$router = new Router();
$exceptionHandler = new ExceptionHandler();

$router->get('/health', static fn (Request $request): JsonResponse => new JsonResponse(['status' => 'ok']));
$router->post('/api/accounts', $container->make(CreateAccountController::class));
$router->get('/api/accounts', $container->make(ListAccountsController::class));
$router->get('/api/accounts/{id}', $container->make(GetAccountController::class));
$router->post('/api/accounts/{id}/credits', $container->make(CreditAccountController::class));
$router->post('/api/accounts/{id}/debits', $container->make(DebitAccountController::class));
$router->get('/accounts', $container->make(ListAccountsPageController::class));
$router->get('/accounts/create', $container->make(CreateAccountPageController::class));
$router->post('/accounts', $container->make(StoreAccountController::class));
$router->get('/accounts/{id}', $container->make(GetAccountPageController::class));

try {
    $response = $router->dispatch(Request::fromGlobals());
} catch (\Throwable $exception) {
    $response = $exceptionHandler->handle($exception);
}

$response->send();
