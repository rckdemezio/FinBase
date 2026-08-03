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
use Demezio\Finbase\Finance\Infrastructure\Persistence\Pdo\PdoTransactionRepository;

$config = require __DIR__.'/../config/app.php';
/** @var array{persistence_driver: string, driver: string, host: string, port: int, database: string, username: string, password: string} $database */
$database = require __DIR__.'/../config/database.php';

$container = new Container();

$container->instance(
    View::class,
    new View($config['views']),
);

$connection = $database['persistence_driver'] === 'mysql'
    ? PdoConnectionFactory::create($database)
    : null;

$transactions = $connection === null
    ? new JsonTransactionRepository($config['storage']['transactions'])
    : new PdoTransactionRepository($connection);

$container->instance(TransactionRepository::class, $transactions);

$accounts = $connection === null
    ? new JsonAccountRepository($config['storage']['accounts'])
    : new PdoAccountRepository($connection);

$container->instance(AccountRepository::class, $accounts);

$container->instance(
    CategoryRepository::class,
    new JsonCategoryRepository(
        $config['storage']['categories'],
    ),
);

return $container;
