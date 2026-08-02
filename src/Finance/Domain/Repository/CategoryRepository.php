<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Domain\Repository;

use Demezio\Finbase\Finance\Domain\Entity\Category;
use Demezio\Finbase\Finance\Domain\ValueObject\CategoryId;

/**
 * Define a persistência de categorias globais.
 */
interface CategoryRepository
{
    public function save(Category $category): void;

    public function findById(CategoryId $id): ?Category;

    /**
     * Busca pelo nome ignorando espaços externos e diferenças entre maiúsculas
     * e minúsculas.
     */
    public function findByName(string $name): ?Category;

    /**
     * @return list<Category>
     */
    public function all(): array;
}
