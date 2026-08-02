<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Infrastructure\Persistence\Json;

use Demezio\Finbase\Finance\Domain\Entity\Category;
use Demezio\Finbase\Finance\Domain\Repository\CategoryRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\CategoryId;
use Demezio\Finbase\Finance\Infrastructure\Exception\PersistenceException;

/**
 * Persiste categorias como uma lista de dados JSON em um arquivo local.
 */
final class JsonCategoryRepository implements CategoryRepository
{
    public function __construct(private readonly string $filePath)
    {
    }

    public function save(Category $category): void
    {
        $categories = $this->read();
        $categories[$category->id()->value()] = $category;

        $records = array_map(
            fn (Category $storedCategory): array => $this->serialize($storedCategory),
            $categories,
        );

        try {
            $json = json_encode(
                array_values($records),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
            );
        } catch (\JsonException $exception) {
            throw new PersistenceException('Não foi possível codificar as categorias em JSON.', previous: $exception);
        }

        if (@file_put_contents($this->filePath, $json, LOCK_EX) === false) {
            throw new PersistenceException(sprintf('Não foi possível gravar o arquivo "%s".', $this->filePath));
        }
    }

    public function findById(CategoryId $id): ?Category
    {
        return $this->read()[$id->value()] ?? null;
    }

    public function findByName(string $name): ?Category
    {
        $normalizedName = self::normalizeName($name);

        foreach ($this->read() as $category) {
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
        return array_values($this->read());
    }

    /**
     * @return array<string, Category>
     */
    private function read(): array
    {
        if (! file_exists($this->filePath)) {
            return [];
        }

        $json = @file_get_contents($this->filePath);

        if ($json === false) {
            throw new PersistenceException(sprintf('Não foi possível ler o arquivo "%s".', $this->filePath));
        }

        try {
            $records = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new PersistenceException('O arquivo de categorias contém JSON inválido.', previous: $exception);
        }

        if (! is_array($records) || ! array_is_list($records)) {
            throw new PersistenceException('O arquivo de categorias deve conter uma lista JSON.');
        }

        $categories = [];

        foreach ($records as $record) {
            $category = $this->deserialize($record);
            $categories[$category->id()->value()] = $category;
        }

        return $categories;
    }

    /**
     * @return array{id: string, name: string}
     */
    private function serialize(Category $category): array
    {
        return [
            'id' => $category->id()->value(),
            'name' => $category->name(),
        ];
    }

    private function deserialize(mixed $record): Category
    {
        if (
            ! is_array($record)
            || ! is_string($record['id'] ?? null)
            || ! is_string($record['name'] ?? null)
        ) {
            throw new PersistenceException('O arquivo de categorias contém um registro inválido.');
        }

        try {
            return Category::restore(
                CategoryId::fromString($record['id']),
                $record['name'],
            );
        } catch (\Throwable $exception) {
            throw new PersistenceException('O arquivo de categorias contém dados inválidos.', previous: $exception);
        }
    }

    private static function normalizeName(string $name): string
    {
        return mb_strtolower(trim($name), 'UTF-8');
    }
}
