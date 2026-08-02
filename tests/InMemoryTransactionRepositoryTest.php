<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Infrastructure\Persistence\InMemory;

use Demezio\Finbase\Finance\Domain\Repository\TransactionRepository;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryTransactionRepository;
use Demezio\Finbase\Tests\Contract\TransactionRepositoryContractTestCase;

final class InMemoryTransactionRepositoryTest extends TransactionRepositoryContractTestCase
{
    protected function createRepository(): TransactionRepository
    {
        return new InMemoryTransactionRepository();
    }
}
