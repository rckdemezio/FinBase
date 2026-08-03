<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests;

use Demezio\Finbase\Core\Contracts\ContainerInterface;
use Demezio\Finbase\Finance\Application\UseCase\CreateAccount\CreateAccount;
use Demezio\Finbase\Finance\Domain\Repository\AccountRepository;
use Demezio\Finbase\Finance\Domain\Repository\TransactionRepository;
use Demezio\Finbase\Finance\Infrastructure\Persistence\Json\JsonAccountRepository;
use Demezio\Finbase\Finance\Infrastructure\Persistence\Json\JsonTransactionRepository;
use Demezio\Finbase\Finance\Infrastructure\Persistence\Pdo\PdoAccountRepository;
use PHPUnit\Framework\TestCase;

final class BootstrapTest extends TestCase
{
    public function testItConfiguresTheAccountRepositoryBinding(): void
    {
        /** @var ContainerInterface $container */
        $container = require __DIR__.'/../bootstrap/app.php';

        self::assertInstanceOf(ContainerInterface::class, $container);
        /** @var array{persistence_driver: string} $database */
        $database = require __DIR__.'/../config/database.php';
        $expectedRepository = $database['persistence_driver'] === 'mysql'
            ? PdoAccountRepository::class
            : JsonAccountRepository::class;

        self::assertInstanceOf($expectedRepository, $container->make(AccountRepository::class));
        self::assertSame(
            $container->make(AccountRepository::class),
            $container->make(AccountRepository::class),
        );
        self::assertInstanceOf(JsonTransactionRepository::class, $container->make(TransactionRepository::class));
    }

    public function testItResolvesUseCasesThroughTheirRepositoryAbstraction(): void
    {
        /** @var ContainerInterface $container */
        $container = require __DIR__.'/../bootstrap/app.php';

        self::assertInstanceOf(CreateAccount::class, $container->make(CreateAccount::class));
    }
}
