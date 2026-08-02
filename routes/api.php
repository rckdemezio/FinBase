<?php

declare(strict_types=1);

use Demezio\Finbase\Core\Contracts\ContainerInterface;
use Demezio\Finbase\Core\Http\JsonResponse;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\Routing\Router;
use Demezio\Finbase\Finance\Presentation\Http\Controller\CreateAccountController;
use Demezio\Finbase\Finance\Presentation\Http\Controller\CreditAccountController;
use Demezio\Finbase\Finance\Presentation\Http\Controller\DebitAccountController;
use Demezio\Finbase\Finance\Presentation\Http\Controller\GetAccountController;
use Demezio\Finbase\Finance\Presentation\Http\Controller\GetMonthlySummaryController;
use Demezio\Finbase\Finance\Presentation\Http\Controller\ListAccountsController;
use Demezio\Finbase\Finance\Presentation\Http\Controller\ListTransactionsController;

return static function (Router $router, ContainerInterface $container): void {
    $router->get('/health', static fn (Request $request): JsonResponse => new JsonResponse(['status' => 'ok']));
    $router->post('/api/accounts', $container->make(CreateAccountController::class));
    $router->get('/api/accounts', $container->make(ListAccountsController::class));
    $router->get('/api/accounts/{id}', $container->make(GetAccountController::class));
    $router->post('/api/accounts/{id}/credits', $container->make(CreditAccountController::class));
    $router->post('/api/accounts/{id}/debits', $container->make(DebitAccountController::class));
    $router->get('/api/accounts/{id}/transactions', $container->make(ListTransactionsController::class));
    $router->get('/api/accounts/{id}/summary', $container->make(GetMonthlySummaryController::class));
};
