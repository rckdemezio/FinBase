<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Infrastructure\Persistence\InMemory;

use Demezio\Finbase\Finance\Domain\Repository\CategoryRepository;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryCategoryRepository;
use Demezio\Finbase\Tests\Contract\CategoryRepositoryContractTestCase;

final class InMemoryCategoryRepositoryTest extends CategoryRepositoryContractTestCase
{
    protected function createRepository(): CategoryRepository
    {
        return new InMemoryCategoryRepository();
    }
}
