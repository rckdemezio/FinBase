<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Application\UseCase\DebitAccount;

use Demezio\Finbase\Finance\Application\Exception\AccountNotFoundException;
use Demezio\Finbase\Finance\Application\UseCase\DebitAccount\DebitAccount;
use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\Exception\CurrencyMismatchException;
use Demezio\Finbase\Finance\Domain\Exception\InsufficientFundsException;
use Demezio\Finbase\Finance\Domain\Exception\InvalidAccountAmountException;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryAccountRepository;
use PHPUnit\Framework\TestCase;

final class DebitAccountTest extends TestCase
{
    public function testDebitsAndPersistsExistingAccount(): void
    {
        $repository = new InMemoryAccountRepository();
        $account = Account::open($this->accountId(), 'Conta principal', 'BRL');
        $account->credit(new Money(1500, 'BRL'));
        $repository->save($account);

        $debitedAccount = (new DebitAccount($repository))->execute($account->id(), new Money(500, 'BRL'));

        self::assertTrue($debitedAccount->balance()->equals(new Money(1000, 'BRL')));

        $persistedAccount = $repository->findById($account->id());

        self::assertNotNull($persistedAccount);
        self::assertTrue($persistedAccount->balance()->equals(new Money(1000, 'BRL')));
    }

    public function testThrowsApplicationExceptionWhenAccountIsNotFound(): void
    {
        $this->expectException(AccountNotFoundException::class);

        (new DebitAccount(new InMemoryAccountRepository()))->execute($this->accountId(), new Money(500, 'BRL'));
    }

    public function testPropagatesDomainExceptionForInsufficientFunds(): void
    {
        $repository = new InMemoryAccountRepository();
        $account = Account::open($this->accountId(), 'Conta principal', 'BRL');
        $repository->save($account);

        $this->expectException(InsufficientFundsException::class);

        (new DebitAccount($repository))->execute($account->id(), new Money(1, 'BRL'));
    }

    public function testPropagatesDomainExceptionForIncompatibleCurrency(): void
    {
        $repository = new InMemoryAccountRepository();
        $account = Account::open($this->accountId(), 'Conta principal', 'BRL');
        $repository->save($account);

        $this->expectException(CurrencyMismatchException::class);

        (new DebitAccount($repository))->execute($account->id(), new Money(500, 'USD'));
    }

    public function testPropagatesDomainExceptionForNonPositiveAmount(): void
    {
        $repository = new InMemoryAccountRepository();
        $account = Account::open($this->accountId(), 'Conta principal', 'BRL');
        $repository->save($account);

        $this->expectException(InvalidAccountAmountException::class);

        (new DebitAccount($repository))->execute($account->id(), new Money(0, 'BRL'));
    }

    private function accountId(): AccountId
    {
        return AccountId::fromString('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d');
    }
}
