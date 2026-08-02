<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Application\UseCase\CreateAccount;

use Demezio\Finbase\Finance\Application\UseCase\CreateAccount\CreateAccount;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryAccountRepository;
use PHPUnit\Framework\TestCase;

final class CreateAccountTest extends TestCase
{
    public function testCreatesAndPersistsAccount(): void
    {
        $repository = new InMemoryAccountRepository();
        $useCase = new CreateAccount($repository);

        $account = $useCase->execute('Conta principal', 'brl');

        self::assertSame('Conta principal', $account->name());
        self::assertTrue($account->balance()->isZero());
        self::assertSame('BRL', $account->balance()->currencyCode());

        $persistedAccount = $repository->findById($account->id());

        self::assertNotNull($persistedAccount);
        self::assertTrue($account->equals($persistedAccount));
    }

    public function testGeneratesDifferentIdentifiersForCreatedAccounts(): void
    {
        $useCase = new CreateAccount(new InMemoryAccountRepository());

        $first = $useCase->execute('Conta principal', 'BRL');
        $second = $useCase->execute('Reserva', 'BRL');

        self::assertFalse($first->id()->equals($second->id()));
    }
}
