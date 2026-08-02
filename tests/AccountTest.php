<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Domain\Entity;

use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\Exception\CurrencyMismatchException;
use Demezio\Finbase\Finance\Domain\Exception\InsufficientFundsException;
use Demezio\Finbase\Finance\Domain\Exception\InvalidAccountAmountException;
use Demezio\Finbase\Finance\Domain\Exception\InvalidAccountBalanceException;
use Demezio\Finbase\Finance\Domain\Exception\InvalidAccountNameException;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

final class AccountTest extends TestCase
{
    public function testOpensAccountWithZeroBalance(): void
    {
        $id = $this->accountId('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d');

        $account = Account::open($id, ' Conta principal ', 'brl');

        self::assertSame($id, $account->id());
        self::assertSame('Conta principal', $account->name());
        self::assertTrue($account->balance()->isZero());
        self::assertSame('BRL', $account->balance()->currencyCode());
    }

    public function testRejectsEmptyNameWhenOpeningAccount(): void
    {
        $this->expectException(InvalidAccountNameException::class);

        Account::open(AccountId::generate(), '   ', 'BRL');
    }

    public function testRestoresPersistedAccount(): void
    {
        $id = $this->accountId('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d');
        $balance = new Money(1250, 'BRL');

        $account = Account::restore($id, 'Conta principal', $balance);

        self::assertSame($id, $account->id());
        self::assertSame('Conta principal', $account->name());
        self::assertSame($balance, $account->balance());
    }

    public function testRejectsNegativeBalanceWhenRestoringAccount(): void
    {
        $this->expectException(InvalidAccountBalanceException::class);

        Account::restore(AccountId::generate(), 'Conta principal', new Money(-1, 'BRL'));
    }

    public function testRenamesAccount(): void
    {
        $account = Account::open(AccountId::generate(), 'Conta principal', 'BRL');

        $account->rename('Reserva');

        self::assertSame('Reserva', $account->name());
    }

    public function testRejectsEmptyNameWhenRenamingAccount(): void
    {
        $account = Account::open(AccountId::generate(), 'Conta principal', 'BRL');

        $this->expectException(InvalidAccountNameException::class);

        $account->rename('  ');
    }

    public function testCreditsAccount(): void
    {
        $account = Account::open(AccountId::generate(), 'Conta principal', 'BRL');

        $account->credit(new Money(1000, 'BRL'));

        self::assertTrue($account->balance()->equals(new Money(1000, 'BRL')));
    }

    public function testDebitsAccount(): void
    {
        $account = Account::open(AccountId::generate(), 'Conta principal', 'BRL');
        $account->credit(new Money(1000, 'BRL'));

        $account->debit(new Money(400, 'BRL'));

        self::assertTrue($account->balance()->equals(new Money(600, 'BRL')));
    }

    public function testRejectsDebitThatWouldMakeBalanceNegative(): void
    {
        $account = Account::open(AccountId::generate(), 'Conta principal', 'BRL');
        $account->credit(new Money(1000, 'BRL'));

        try {
            $account->debit(new Money(1001, 'BRL'));
            self::fail('Era esperada uma exceção de saldo insuficiente.');
        } catch (InsufficientFundsException) {
            self::assertTrue($account->balance()->equals(new Money(1000, 'BRL')));
        }
    }

    public function testRejectsCreditWithDifferentCurrency(): void
    {
        $account = Account::open(AccountId::generate(), 'Conta principal', 'BRL');

        $this->expectException(CurrencyMismatchException::class);

        $account->credit(new Money(1000, 'USD'));
    }

    public function testRejectsDebitWithDifferentCurrency(): void
    {
        $account = Account::open(AccountId::generate(), 'Conta principal', 'BRL');

        $this->expectException(CurrencyMismatchException::class);

        $account->debit(new Money(1000, 'USD'));
    }

    public function testRejectsNonPositiveOperationAmount(): void
    {
        $account = Account::open(AccountId::generate(), 'Conta principal', 'BRL');

        $this->expectException(InvalidAccountAmountException::class);

        $account->credit(new Money(0, 'BRL'));
    }

    public function testComparesAccountsByIdentifier(): void
    {
        $id = $this->accountId('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d');
        $sameId = $this->accountId('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d');
        $otherId = $this->accountId('2a8e5d4c-7041-4d49-9310-c9a389c4cc9f');

        $account = Account::open($id, 'Conta principal', 'BRL');
        $sameAccount = Account::restore($sameId, 'Outro nome', new Money(500, 'BRL'));
        $otherAccount = Account::open($otherId, 'Conta principal', 'BRL');

        self::assertTrue($account->equals($sameAccount));
        self::assertFalse($account->equals($otherAccount));
    }

    private function accountId(string $value): AccountId
    {
        return AccountId::fromString($value);
    }
}
