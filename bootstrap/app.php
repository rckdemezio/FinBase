<?php

declare(strict_types=1);

use Demezio\Finbase\Core\Container\Container;
use Demezio\Finbase\Finance\Domain\Repository\AccountRepository;
use Demezio\Finbase\Finance\Infrastructure\Persistence\Json\JsonAccountRepository;

$config = require __DIR__.'/../config/app.php';

$container = new Container();

$container->instance(
    AccountRepository::class,
    new JsonAccountRepository(
        $config['storage']['accounts'],
    ),
);

return $container;
