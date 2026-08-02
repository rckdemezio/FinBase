<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Application\UseCase\CreateCategory;

use Demezio\Finbase\Finance\Application\Exception\DuplicateCategoryNameException;
use Demezio\Finbase\Finance\Domain\Entity\Category;
use Demezio\Finbase\Finance\Domain\Repository\CategoryRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\CategoryId;

/**
 * Cria e persiste uma categoria global com nome único.
 */
final class CreateCategory
{
    public function __construct(private readonly CategoryRepository $repository)
    {
    }

    /**
     * @throws DuplicateCategoryNameException
     */
    public function execute(string $name): Category
    {
        if ($this->repository->findByName($name) !== null) {
            throw new DuplicateCategoryNameException('Já existe uma categoria com este nome.');
        }

        $category = Category::create(CategoryId::generate(), $name);
        $this->repository->save($category);

        return $category;
    }
}
