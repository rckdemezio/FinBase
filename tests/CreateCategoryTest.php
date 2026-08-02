<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Application\UseCase\CreateCategory;

use Demezio\Finbase\Finance\Application\Exception\DuplicateCategoryNameException;
use Demezio\Finbase\Finance\Application\UseCase\CreateCategory\CreateCategory;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryCategoryRepository;
use PHPUnit\Framework\TestCase;

final class CreateCategoryTest extends TestCase
{
    public function testCreatesAndPersistsACategory(): void
    {
        $repository = new InMemoryCategoryRepository();

        $category = (new CreateCategory($repository))->execute(' Alimentação ');

        self::assertSame('Alimentação', $category->name());
        self::assertNotNull($repository->findById($category->id()));
    }

    public function testRejectsAnExistingNameIgnoringCaseAndOuterWhitespace(): void
    {
        $useCase = new CreateCategory(new InMemoryCategoryRepository());
        $useCase->execute('Alimentação');

        $this->expectException(DuplicateCategoryNameException::class);

        $useCase->execute('  ALIMENTAÇÃO  ');
    }
}
