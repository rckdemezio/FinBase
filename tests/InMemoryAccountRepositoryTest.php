<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Infrastructure\Persistence\InMemory;

use Demezio\Finbase\Finance\Domain\Repository\AccountRepository;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryAccountRepository;
use Demezio\Finbase\Tests\Contract\AccountRepositoryContractTestCase;

final class InMemoryAccountRepositoryTest extends AccountRepositoryContractTestCase
{
    protected function createRepository(): AccountRepository
    {
        return new InMemoryAccountRepository();
    }
}
