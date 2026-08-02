<?php

declare(strict_types=1);

use Demezio\Finbase\Core\Container\Container;
use Demezio\Finbase\Core\View\View;
use Demezio\Finbase\Finance\Domain\Repository\AccountRepository;
use Demezio\Finbase\Finance\Domain\Repository\TransactionRepository;
use Demezio\Finbase\Finance\Infrastructure\Persistence\Json\JsonAccountRepository;
use Demezio\Finbase\Finance\Infrastructure\Persistence\Json\JsonTransactionRepository;

$config = require __DIR__.'/../config/app.php';

$container = new Container();

$container->instance(
    View::class,
    new View($config['views']),
);

$container->instance(
    TransactionRepository::class,
    new JsonTransactionRepository(
        $config['storage']['transactions'],
    ),
);

$container->instance(
    AccountRepository::class,
    new JsonAccountRepository(
        $config['storage']['accounts'],
    ),
);

return $container;
