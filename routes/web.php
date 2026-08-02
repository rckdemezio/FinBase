<?php

declare(strict_types=1);

use Demezio\Finbase\Core\Contracts\ContainerInterface;
use Demezio\Finbase\Core\Routing\Router;
use Demezio\Finbase\Finance\Presentation\Web\Controller\CreateAccountPageController;
use Demezio\Finbase\Finance\Presentation\Web\Controller\CreateExpensePageController;
use Demezio\Finbase\Finance\Presentation\Web\Controller\CreateIncomePageController;
use Demezio\Finbase\Finance\Presentation\Web\Controller\GetAccountPageController;
use Demezio\Finbase\Finance\Presentation\Web\Controller\GetMonthlySummaryPageController;
use Demezio\Finbase\Finance\Presentation\Web\Controller\ListAccountsPageController;
use Demezio\Finbase\Finance\Presentation\Web\Controller\ListTransactionsPageController;
use Demezio\Finbase\Finance\Presentation\Web\Controller\StoreAccountController;
use Demezio\Finbase\Finance\Presentation\Web\Controller\StoreExpenseController;
use Demezio\Finbase\Finance\Presentation\Web\Controller\StoreIncomeController;

return static function (Router $router, ContainerInterface $container): void {
    $router->get('/accounts', $container->make(ListAccountsPageController::class));
    $router->get('/accounts/create', $container->make(CreateAccountPageController::class));
    $router->post('/accounts', $container->make(StoreAccountController::class));
    $router->get('/accounts/{id}/income/create', $container->make(CreateIncomePageController::class));
    $router->post('/accounts/{id}/income', $container->make(StoreIncomeController::class));
    $router->get('/accounts/{id}/expenses/create', $container->make(CreateExpensePageController::class));
    $router->post('/accounts/{id}/expenses', $container->make(StoreExpenseController::class));
    $router->get('/accounts/{id}', $container->make(GetAccountPageController::class));
    $router->get('/accounts/{id}/transactions', $container->make(ListTransactionsPageController::class));
    $router->get('/accounts/{id}/summary', $container->make(GetMonthlySummaryPageController::class));
};
