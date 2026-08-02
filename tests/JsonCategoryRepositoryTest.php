<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Infrastructure\Persistence\Json;

use Demezio\Finbase\Finance\Domain\Entity\Category;
use Demezio\Finbase\Finance\Domain\Repository\CategoryRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\CategoryId;
use Demezio\Finbase\Finance\Infrastructure\Exception\PersistenceException;
use Demezio\Finbase\Finance\Infrastructure\Persistence\Json\JsonCategoryRepository;
use Demezio\Finbase\Tests\Contract\CategoryRepositoryContractTestCase;

final class JsonCategoryRepositoryTest extends CategoryRepositoryContractTestCase
{
    private string $filePath;

    protected function setUp(): void
    {
        $this->filePath = sys_get_temp_dir().'/finbase-categories-'.bin2hex(random_bytes(8)).'.json';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }

    protected function createRepository(): CategoryRepository
    {
        return new JsonCategoryRepository($this->filePath);
    }

    public function testItCreatesTheFileAndUsesAStableDataRepresentation(): void
    {
        $category = Category::create(
            CategoryId::fromString('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d'),
            'Alimentação',
        );

        $this->createRepository()->save($category);

        self::assertFileExists($this->filePath);
        self::assertSame(
            [[
                'id' => $category->id()->value(),
                'name' => 'Alimentação',
            ]],
            json_decode((string) file_get_contents($this->filePath), true, 512, JSON_THROW_ON_ERROR),
        );
    }

    public function testItPersistsCategoriesBetweenRepositoryInstances(): void
    {
        $category = Category::create(
            CategoryId::fromString('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d'),
            'Alimentação',
        );

        $this->createRepository()->save($category);
        $found = $this->createRepository()->findById($category->id());

        self::assertNotNull($found);
        self::assertSame('Alimentação', $found->name());
    }

    public function testItThrowsAPredictableExceptionForAnEmptyOrInvalidFile(): void
    {
        file_put_contents($this->filePath, '');

        $this->expectException(PersistenceException::class);

        $this->createRepository()->all();
    }
}
