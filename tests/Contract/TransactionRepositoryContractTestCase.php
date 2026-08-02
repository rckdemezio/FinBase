<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Contract;

use Demezio\Finbase\Finance\Domain\Entity\Transaction;
use Demezio\Finbase\Finance\Domain\Enum\TransactionType;
use Demezio\Finbase\Finance\Domain\Repository\TransactionRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use Demezio\Finbase\Finance\Domain\ValueObject\TransactionId;
use PHPUnit\Framework\TestCase;

abstract class TransactionRepositoryContractTestCase extends TestCase
{
    abstract protected function createRepository(): TransactionRepository;

    public function testSavesAndFindsTransactionByIdentifier(): void
    {
        $repository = $this->createRepository();
        $transaction = $this->transaction('550e8400-e29b-41d4-a716-446655440000', TransactionType::CREDIT, 500000);

        $repository->save($transaction);

        $found = $repository->findById($transaction->id());

        self::assertNotNull($found);
        self::assertTrue($transaction->id()->equals($found->id()));
        self::assertSame(TransactionType::CREDIT, $found->type());
        self::assertTrue($found->amount()->equals(new Money(500000, 'BRL')));
        self::assertSame('Salário', $found->description());
    }

    public function testReturnsNullWhenTransactionIsNotFound(): void
    {
        self::assertNull($this->createRepository()->findById($this->transactionId('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d')));
    }

    public function testUpdatesTransactionWithSameIdentifier(): void
    {
        $repository = $this->createRepository();
        $id = '550e8400-e29b-41d4-a716-446655440000';
        $repository->save($this->transaction($id, TransactionType::CREDIT, 500000));
        $repository->save($this->transaction($id, TransactionType::DEBIT, 150000));

        $found = $repository->findById($this->transactionId($id));

        self::assertNotNull($found);
        self::assertSame(TransactionType::DEBIT, $found->type());
        self::assertTrue($found->amount()->equals(new Money(150000, 'BRL')));
        self::assertCount(1, $repository->all());
    }

    public function testReturnsAllSavedTransactions(): void
    {
        $repository = $this->createRepository();
        $first = $this->transaction('550e8400-e29b-41d4-a716-446655440000', TransactionType::CREDIT, 500000);
        $second = $this->transaction('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d', TransactionType::DEBIT, 150000);

        $repository->save($first);
        $repository->save($second);

        $transactions = $repository->all();
        $identifiers = array_map(static fn (Transaction $transaction): string => $transaction->id()->value(), $transactions);

        self::assertCount(2, $transactions);
        self::assertContains($first->id()->value(), $identifiers);
        self::assertContains($second->id()->value(), $identifiers);
    }

    private function transaction(string $id, TransactionType $type, int $amount): Transaction
    {
        return Transaction::record(
            $this->transactionId($id),
            AccountId::fromString('d9428888-122b-11e1-b85c-61cd3cbb3210'),
            $type,
            new Money($amount, 'BRL'),
            'Salário',
            new \DateTimeImmutable('2026-08-02 14:30:00-03:00'),
        );
    }

    private function transactionId(string $id): TransactionId
    {
        return TransactionId::fromString($id);
    }
}
