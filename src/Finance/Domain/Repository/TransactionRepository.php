<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Domain\Repository;

use Demezio\Finbase\Finance\Domain\Entity\Transaction;
use Demezio\Finbase\Finance\Domain\ValueObject\TransactionId;

/**
 * Define a persistência do histórico de transações financeiras.
 */
interface TransactionRepository
{
    public function save(Transaction $transaction): void;

    public function findById(TransactionId $id): ?Transaction;

    /**
     * @return list<Transaction>
     */
    public function all(): array;
}
