<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Application\UseCase\CreditAccount;

use Demezio\Finbase\Finance\Application\Exception\AccountNotFoundException;
use Demezio\Finbase\Finance\Application\UseCase\CreditAccount\CreditAccount;
use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\Exception\CurrencyMismatchException;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryAccountRepository;
use PHPUnit\Framework\TestCase;

final class CreditAccountTest extends TestCase
{
    public function testCreditsAndPersistsExistingAccount(): void
    {
        $repository = new InMemoryAccountRepository();
        $account = Account::open($this->accountId(), 'Conta principal', 'BRL');
        $repository->save($account);

        $creditedAccount = (new CreditAccount($repository))->execute($account->id(), new Money(1500, 'BRL'));

        self::assertTrue($creditedAccount->balance()->equals(new Money(1500, 'BRL')));

        $persistedAccount = $repository->findById($account->id());

        self::assertNotNull($persistedAccount);
        self::assertTrue($persistedAccount->balance()->equals(new Money(1500, 'BRL')));
    }

    public function testThrowsApplicationExceptionWhenAccountIsNotFound(): void
    {
        $this->expectException(AccountNotFoundException::class);

        (new CreditAccount(new InMemoryAccountRepository()))->execute($this->accountId(), new Money(1500, 'BRL'));
    }

    public function testPropagatesDomainExceptionForIncompatibleCurrency(): void
    {
        $repository = new InMemoryAccountRepository();
        $account = Account::open($this->accountId(), 'Conta principal', 'BRL');
        $repository->save($account);

        $this->expectException(CurrencyMismatchException::class);

        (new CreditAccount($repository))->execute($account->id(), new Money(1500, 'USD'));
    }

    private function accountId(): AccountId
    {
        return AccountId::fromString('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d');
    }
}
