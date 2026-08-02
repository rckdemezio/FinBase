<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Domain\Entity;

use Demezio\Finbase\Finance\Domain\Entity\Transaction;
use Demezio\Finbase\Finance\Domain\Enum\TransactionType;
use Demezio\Finbase\Finance\Domain\Exception\InvalidTransactionAmountException;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use Demezio\Finbase\Finance\Domain\ValueObject\TransactionId;
use PHPUnit\Framework\TestCase;

final class TransactionTest extends TestCase
{
    public function testItRecordsAnImmutableCreditTransaction(): void
    {
        $occurredAt = new \DateTimeImmutable('2026-08-02 10:30:00+00:00');
        $transaction = Transaction::record(
            $this->transactionId(),
            $this->accountId(),
            TransactionType::CREDIT,
            new Money(500000, 'BRL'),
            'Salário',
            $occurredAt,
        );

        self::assertTrue($transaction->id()->equals($this->transactionId()));
        self::assertTrue($transaction->accountId()->equals($this->accountId()));
        self::assertSame(TransactionType::CREDIT, $transaction->type());
        self::assertTrue($transaction->amount()->equals(new Money(500000, 'BRL')));
        self::assertSame('Salário', $transaction->description());
        self::assertSame($occurredAt, $transaction->occurredAt());
    }

    public function testItAllowsAnEmptyDescription(): void
    {
        $transaction = Transaction::record(
            $this->transactionId(),
            $this->accountId(),
            TransactionType::DEBIT,
            new Money(150000, 'BRL'),
            '',
            new \DateTimeImmutable('2026-08-02'),
        );

        self::assertSame('', $transaction->description());
    }

    public function testItRestoresAPersistedTransaction(): void
    {
        $transaction = Transaction::restore(
            $this->transactionId(),
            $this->accountId(),
            TransactionType::DEBIT,
            new Money(150000, 'BRL'),
            'Aluguel',
            new \DateTimeImmutable('2026-08-01 00:00:00+00:00'),
        );

        self::assertSame(TransactionType::DEBIT, $transaction->type());
        self::assertSame('Aluguel', $transaction->description());
    }

    public function testItRejectsZeroOrNegativeAmounts(): void
    {
        $this->expectException(InvalidTransactionAmountException::class);

        Transaction::record(
            $this->transactionId(),
            $this->accountId(),
            TransactionType::DEBIT,
            new Money(0, 'BRL'),
            '',
            new \DateTimeImmutable(),
        );
    }

    public function testTransactionTypeUsesTheDirectionOfTheMovement(): void
    {
        self::assertSame('CREDIT', TransactionType::CREDIT->value);
        self::assertSame('DEBIT', TransactionType::DEBIT->value);
    }

    private function transactionId(): TransactionId
    {
        return TransactionId::fromString('550e8400-e29b-41d4-a716-446655440000');
    }

    private function accountId(): AccountId
    {
        return AccountId::fromString('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d');
    }
}
