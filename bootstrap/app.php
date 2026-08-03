<?php

declare(strict_types=1);

use Demezio\Finbase\Core\Container\Container;
use Demezio\Finbase\Core\View\View;
use Demezio\Finbase\Finance\Domain\Repository\AccountRepository;
use Demezio\Finbase\Finance\Domain\Repository\CategoryRepository;
use Demezio\Finbase\Finance\Domain\Repository\TransactionRepository;
use Demezio\Finbase\Finance\Infrastructure\Database\PdoConnectionFactory;
use Demezio\Finbase\Finance\Infrastructure\Persistence\Json\JsonAccountRepository;
use Demezio\Finbase\Finance\Infrastructure\Persistence\Json\JsonCategoryRepository;
use Demezio\Finbase\Finance\Infrastructure\Persistence\Json\JsonTransactionRepository;
use Demezio\Finbase\Finance\Infrastructure\Persistence\Pdo\PdoAccountRepository;

$config = require __DIR__.'/../config/app.php';
/** @var array{persistence_driver: string, driver: string, host: string, port: int, database: string, username: string, password: string} $database */
$database = require __DIR__.'/../config/database.php';

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

$accounts = $database['persistence_driver'] === 'mysql'
    ? new PdoAccountRepository(PdoConnectionFactory::create($database))
    : new JsonAccountRepository($config['storage']['accounts']);

$container->instance(AccountRepository::class, $accounts);

$container->instance(
    CategoryRepository::class,
    new JsonCategoryRepository(
        $config['storage']['categories'],
    ),
);

return $container;
