<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Domain\Entity;

use Demezio\Finbase\Finance\Domain\Exception\InvalidCategoryNameException;
use Demezio\Finbase\Finance\Domain\ValueObject\CategoryId;

/**
 * Representa uma classificação global para transações financeiras.
 */
final class Category
{
    private function __construct(
        private readonly CategoryId $id,
        private string $name,
    ) {
    }

    /**
     * @throws InvalidCategoryNameException
     */
    public static function create(CategoryId $id, string $name): self
    {
        return new self($id, self::normalizeName($name));
    }

    /**
     * @throws InvalidCategoryNameException
     */
    public static function restore(CategoryId $id, string $name): self
    {
        return new self($id, self::normalizeName($name));
    }

    public function id(): CategoryId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * @throws InvalidCategoryNameException
     */
    public function rename(string $name): void
    {
        $this->name = self::normalizeName($name);
    }

    public function equals(self $other): bool
    {
        return $this->id->equals($other->id);
    }

    /**
     * @throws InvalidCategoryNameException
     */
    private static function normalizeName(string $name): string
    {
        $name = trim($name);

        if ($name === '') {
            throw new InvalidCategoryNameException('O nome da categoria não pode ser vazio.');
        }

        return $name;
    }
}
