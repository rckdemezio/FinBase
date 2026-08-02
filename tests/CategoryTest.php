<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Domain\Entity;

use Demezio\Finbase\Finance\Domain\Entity\Category;
use Demezio\Finbase\Finance\Domain\Exception\InvalidCategoryNameException;
use Demezio\Finbase\Finance\Domain\ValueObject\CategoryId;
use PHPUnit\Framework\TestCase;

final class CategoryTest extends TestCase
{
    public function testCreatesCategoryWithAValidNameAndTrimsOuterWhitespace(): void
    {
        $id = $this->id('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d');

        $category = Category::create($id, ' Alimentação ');

        self::assertSame($id, $category->id());
        self::assertSame('Alimentação', $category->name());
    }

    public function testRejectsAnEmptyCategoryName(): void
    {
        $this->expectException(InvalidCategoryNameException::class);

        Category::create(CategoryId::generate(), '   ');
    }

    public function testRenamesACategoryWithoutChangingItsIdentifier(): void
    {
        $category = Category::create($this->id('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d'), 'Alimentação');
        $id = $category->id();

        $category->rename(' Moradia ');

        self::assertSame($id, $category->id());
        self::assertSame('Moradia', $category->name());
    }

    public function testRejectsAnEmptyNameWhenRenaming(): void
    {
        $category = Category::create(CategoryId::generate(), 'Alimentação');

        $this->expectException(InvalidCategoryNameException::class);

        $category->rename('  ');
    }

    public function testEqualityDependsOnlyOnTheIdentifier(): void
    {
        $id = $this->id('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d');
        $sameId = $this->id('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d');
        $otherId = $this->id('2a8e5d4c-7041-4d49-9310-c9a389c4cc9f');

        self::assertTrue(Category::create($id, 'Alimentação')->equals(Category::create($sameId, 'Outro nome')));
        self::assertFalse(Category::create($id, 'Alimentação')->equals(Category::create($otherId, 'Alimentação')));
    }

    private function id(string $value): CategoryId
    {
        return CategoryId::fromString($value);
    }
}
