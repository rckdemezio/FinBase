<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Contract;

use Demezio\Finbase\Finance\Domain\Entity\Category;
use Demezio\Finbase\Finance\Domain\Repository\CategoryRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\CategoryId;
use PHPUnit\Framework\TestCase;

abstract class CategoryRepositoryContractTestCase extends TestCase
{
    abstract protected function createRepository(): CategoryRepository;

    public function testSavesAndFindsCategoryByIdentifier(): void
    {
        $repository = $this->createRepository();
        $category = Category::create($this->categoryId('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d'), 'Alimentação');

        $repository->save($category);

        $found = $repository->findById($category->id());

        self::assertNotNull($found);
        self::assertTrue($category->equals($found));
        self::assertSame('Alimentação', $found->name());
    }

    public function testReturnsNullWhenCategoryIsNotFound(): void
    {
        $category = $this->createRepository()->findById(
            $this->categoryId('2a8e5d4c-7041-4d49-9310-c9a389c4cc9f'),
        );

        self::assertNull($category);
    }

    public function testFindsCategoryByNameIgnoringCaseAndOuterWhitespace(): void
    {
        $repository = $this->createRepository();
        $category = Category::create($this->categoryId('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d'), 'Alimentação');
        $repository->save($category);

        $found = $repository->findByName('  ALIMENTAÇÃO  ');

        self::assertNotNull($found);
        self::assertTrue($category->equals($found));
    }

    public function testReturnsNullWhenCategoryNameIsNotFound(): void
    {
        $repository = $this->createRepository();

        self::assertNull($repository->findByName('Moradia'));
    }

    public function testUpdatesCategoryWithSameIdentifier(): void
    {
        $repository = $this->createRepository();
        $id = $this->categoryId('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d');

        $repository->save(Category::create($id, 'Nome antigo'));
        $repository->save(Category::restore($id, 'Nome atualizado'));

        $found = $repository->findById($id);

        self::assertNotNull($found);
        self::assertSame('Nome atualizado', $found->name());
        self::assertCount(1, $repository->all());
    }

    public function testReturnsAllSavedCategories(): void
    {
        $repository = $this->createRepository();
        $first = Category::create($this->categoryId('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d'), 'Alimentação');
        $second = Category::create($this->categoryId('2a8e5d4c-7041-4d49-9310-c9a389c4cc9f'), 'Moradia');

        $repository->save($first);
        $repository->save($second);

        $categories = $repository->all();
        $identifiers = array_map(static fn (Category $category): string => $category->id()->value(), $categories);

        self::assertCount(2, $categories);
        self::assertContains($first->id()->value(), $identifiers);
        self::assertContains($second->id()->value(), $identifiers);
    }

    private function categoryId(string $value): CategoryId
    {
        return CategoryId::fromString($value);
    }
}
