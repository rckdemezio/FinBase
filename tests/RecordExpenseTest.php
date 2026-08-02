<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Application\UseCase\RecordExpense;

use Demezio\Finbase\Finance\Application\UseCase\RecordExpense\RecordExpense;
use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\Enum\TransactionType;
use Demezio\Finbase\Finance\Domain\Exception\CurrencyMismatchException;
use Demezio\Finbase\Finance\Domain\Exception\InsufficientFundsException;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryAccountRepository;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryTransactionRepository;
use PHPUnit\Framework\TestCase;

final class RecordExpenseTest extends TestCase
{
    public function testItDecreasesTheBalanceAndRecordsADebitTransaction(): void
    {
        $accounts = new InMemoryAccountRepository();
        $transactions = new InMemoryTransactionRepository();
        $account = Account::restore($this->accountId(), 'Conta Principal', new Money(500000, 'BRL'));
        $accounts->save($account);

        $transaction = (new RecordExpense($accounts, $transactions))->execute(
            $account->id(),
            new Money(150000, 'BRL'),
            'Aluguel',
            new \DateTimeImmutable('2026-08-02 14:30:00-03:00'),
        );

        self::assertTrue($account->balance()->equals(new Money(350000, 'BRL')));
        self::assertSame(TransactionType::DEBIT, $transaction->type());
        self::assertSame('Aluguel', $transaction->description());
        self::assertSame($transaction, $transactions->findById($transaction->id()));
    }

    public function testItDoesNotCreateATransactionWhenThereAreInsufficientFunds(): void
    {
        $accounts = new InMemoryAccountRepository();
        $transactions = new InMemoryTransactionRepository();
        $account = Account::open($this->accountId(), 'Conta Principal', 'BRL');
        $accounts->save($account);

        $this->expectException(InsufficientFundsException::class);

        try {
            (new RecordExpense($accounts, $transactions))->execute(
                $account->id(),
                new Money(1, 'BRL'),
                'Mercado',
                new \DateTimeImmutable(),
            );
        } finally {
            self::assertTrue($account->balance()->equals(new Money(0, 'BRL')));
            self::assertSame([], $transactions->all());
        }
    }

    public function testItDoesNotAlterTheAccountOrCreateHistoryForACurrencyMismatch(): void
    {
        $accounts = new InMemoryAccountRepository();
        $transactions = new InMemoryTransactionRepository();
        $account = Account::restore($this->accountId(), 'Conta Principal', new Money(500000, 'BRL'));
        $accounts->save($account);

        $this->expectException(CurrencyMismatchException::class);

        try {
            (new RecordExpense($accounts, $transactions))->execute(
                $account->id(),
                new Money(150000, 'USD'),
                'Aluguel',
                new \DateTimeImmutable(),
            );
        } finally {
            self::assertTrue($account->balance()->equals(new Money(500000, 'BRL')));
            self::assertSame([], $transactions->all());
        }
    }

    private function accountId(): AccountId
    {
        return AccountId::fromString('550e8400-e29b-41d4-a716-446655440000');
    }
}
