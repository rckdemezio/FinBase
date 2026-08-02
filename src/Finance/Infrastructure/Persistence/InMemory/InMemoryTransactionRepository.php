<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory;

use Demezio\Finbase\Finance\Domain\Entity\Transaction;
use Demezio\Finbase\Finance\Domain\Repository\TransactionRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\TransactionId;

/**
 * Armazena transações em memória durante o ciclo de vida do processo atual.
 */
final class InMemoryTransactionRepository implements TransactionRepository
{
    /** @var array<string, Transaction> */
    private array $transactions = [];

    public function save(Transaction $transaction): void
    {
        $this->transactions[$transaction->id()->value()] = $transaction;
    }

    public function findById(TransactionId $id): ?Transaction
    {
        return $this->transactions[$id->value()] ?? null;
    }

    /**
     * @return list<Transaction>
     */
    public function findByAccountId(AccountId $accountId): array
    {
        return array_values(array_filter(
            $this->transactions,
            static fn (Transaction $transaction): bool => $transaction->accountId()->equals($accountId),
        ));
    }

    /**
     * @return list<Transaction>
     */
    public function all(): array
    {
        return array_values($this->transactions);
    }
}
