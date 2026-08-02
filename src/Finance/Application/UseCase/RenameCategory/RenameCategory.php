<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Application\UseCase\RenameCategory;

use Demezio\Finbase\Finance\Application\Exception\CategoryNotFoundException;
use Demezio\Finbase\Finance\Application\Exception\DuplicateCategoryNameException;
use Demezio\Finbase\Finance\Domain\Entity\Category;
use Demezio\Finbase\Finance\Domain\Repository\CategoryRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\CategoryId;

/**
 * Renomeia uma categoria global preservando a unicidade do nome.
 */
final class RenameCategory
{
    public function __construct(private readonly CategoryRepository $repository)
    {
    }

    /**
     * @throws CategoryNotFoundException
     * @throws DuplicateCategoryNameException
     */
    public function execute(CategoryId $id, string $name): Category
    {
        $category = $this->repository->findById($id);

        if ($category === null) {
            throw new CategoryNotFoundException('Categoria não encontrada.');
        }

        $existing = $this->repository->findByName($name);

        if ($existing !== null && ! $existing->id()->equals($category->id())) {
            throw new DuplicateCategoryNameException('Já existe uma categoria com este nome.');
        }

        $category->rename($name);
        $this->repository->save($category);

        return $category;
    }
}
