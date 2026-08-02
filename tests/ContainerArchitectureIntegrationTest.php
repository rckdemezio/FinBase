<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Core\Container;

use Demezio\Finbase\Core\Container\Container;
use Demezio\Finbase\Finance\Application\UseCase\CreateAccount\CreateAccount;
use Demezio\Finbase\Finance\Application\UseCase\CreditAccount\CreditAccount;
use Demezio\Finbase\Finance\Domain\Repository\AccountRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryAccountRepository;
use PHPUnit\Framework\TestCase;

final class ContainerArchitectureIntegrationTest extends TestCase
{
    public function testContainerSharesRepositoryAcrossAutowireResolvedUseCases(): void
    {
        $container = new Container();
        $container->singleton(AccountRepository::class, InMemoryAccountRepository::class);

        $createAccount = $container->make(CreateAccount::class);
        $creditAccount = $container->make(CreditAccount::class);

        self::assertInstanceOf(CreateAccount::class, $createAccount);
        self::assertInstanceOf(CreditAccount::class, $creditAccount);

        $account = $createAccount->execute('Conta principal', 'BRL');
        $creditAccount->execute($account->id(), new Money(10_000, 'BRL'));

        self::assertSame(10_000, $account->balance()->amount());

        $repository = $container->make(AccountRepository::class);

        self::assertInstanceOf(AccountRepository::class, $repository);

        $persistedAccount = $repository->findById($account->id());

        self::assertNotNull($persistedAccount);
        self::assertSame(10_000, $persistedAccount->balance()->amount());
    }
}
