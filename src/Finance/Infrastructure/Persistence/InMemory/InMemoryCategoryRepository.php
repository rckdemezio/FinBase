<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory;

use Demezio\Finbase\Finance\Domain\Entity\Category;
use Demezio\Finbase\Finance\Domain\Repository\CategoryRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\CategoryId;

/**
 * Armazena categorias em memória durante o ciclo de vida do processo atual.
 */
final class InMemoryCategoryRepository implements CategoryRepository
{
    /** @var array<string, Category> */
    private array $categories = [];

    public function save(Category $category): void
    {
        $this->categories[$category->id()->value()] = $category;
    }

    public function findById(CategoryId $id): ?Category
    {
        return $this->categories[$id->value()] ?? null;
    }

    public function findByName(string $name): ?Category
    {
        $normalizedName = self::normalizeName($name);

        foreach ($this->categories as $category) {
            if (self::normalizeName($category->name()) === $normalizedName) {
                return $category;
            }
        }

        return null;
    }

    /**
     * @return list<Category>
     */
    public function all(): array
    {
        return array_values($this->categories);
    }

    private static function normalizeName(string $name): string
    {
        return mb_strtolower(trim($name), 'UTF-8');
    }
}
