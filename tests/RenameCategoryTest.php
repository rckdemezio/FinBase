<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Application\UseCase\RenameCategory;

use Demezio\Finbase\Finance\Application\Exception\CategoryNotFoundException;
use Demezio\Finbase\Finance\Application\Exception\DuplicateCategoryNameException;
use Demezio\Finbase\Finance\Application\UseCase\RenameCategory\RenameCategory;
use Demezio\Finbase\Finance\Domain\Entity\Category;
use Demezio\Finbase\Finance\Domain\Exception\InvalidCategoryNameException;
use Demezio\Finbase\Finance\Domain\ValueObject\CategoryId;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryCategoryRepository;
use PHPUnit\Framework\TestCase;

final class RenameCategoryTest extends TestCase
{
    public function testRenamesAndPersistsTheExistingCategory(): void
    {
        $repository = new InMemoryCategoryRepository();
        $category = Category::create($this->id(), 'Alimentação');
        $id = $category->id();
        $repository->save($category);

        $renamed = (new RenameCategory($repository))->execute($category->id(), 'Moradia');
        $persisted = $repository->findById($category->id());

        self::assertSame($category, $renamed);
        self::assertSame($category, $persisted);
        self::assertSame('Moradia', $renamed->name());
        self::assertTrue($renamed->id()->equals($id));
    }

    public function testRejectsAnUnknownCategory(): void
    {
        $this->expectException(CategoryNotFoundException::class);

        (new RenameCategory(new InMemoryCategoryRepository()))->execute($this->id(), 'Moradia');
    }

    public function testRejectsAnEmptyNameThroughTheEntity(): void
    {
        $repository = new InMemoryCategoryRepository();
        $category = Category::create($this->id(), 'Alimentação');
        $repository->save($category);

        $this->expectException(InvalidCategoryNameException::class);

        (new RenameCategory($repository))->execute($category->id(), '  ');
    }

    public function testRejectsANameThatBelongsToAnotherCategory(): void
    {
        $repository = new InMemoryCategoryRepository();
        $category = Category::create($this->id(), 'Alimentação');
        $repository->save($category);
        $repository->save(Category::create($this->otherId(), 'Moradia'));

        $this->expectException(DuplicateCategoryNameException::class);

        (new RenameCategory($repository))->execute($category->id(), ' moradia ');
    }

    public function testAcceptsACaseAndWhitespaceVariationOfItsOwnName(): void
    {
        $repository = new InMemoryCategoryRepository();
        $category = Category::create($this->id(), 'Alimentação');
        $repository->save($category);

        $renamed = (new RenameCategory($repository))->execute($category->id(), ' alimentação ');

        self::assertSame('alimentação', $renamed->name());
        self::assertCount(1, $repository->all());
    }

    private function id(): CategoryId
    {
        return CategoryId::fromString('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d');
    }

    private function otherId(): CategoryId
    {
        return CategoryId::fromString('2a8e5d4c-7041-4d49-9310-c9a389c4cc9f');
    }
}
