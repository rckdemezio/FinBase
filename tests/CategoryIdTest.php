<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Domain\ValueObject;

use Demezio\Finbase\Finance\Domain\Exception\InvalidCategoryIdException;
use Demezio\Finbase\Finance\Domain\ValueObject\CategoryId;
use PHPUnit\Framework\TestCase;

final class CategoryIdTest extends TestCase
{
    public function testGeneratesDifferentUuidV4Identifiers(): void
    {
        $first = CategoryId::generate();
        $second = CategoryId::generate();

        self::assertNotSame($first->value(), $second->value());
        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/D',
            $first->value(),
        );
    }

    public function testReconstitutesAnIdentifierFromAValidString(): void
    {
        $identifier = CategoryId::fromString('9A5F8D6B-5C49-4C55-8D45-4E12AE41118D');

        self::assertSame('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d', $identifier->value());
    }

    public function testRejectsAnInvalidIdentifierString(): void
    {
        $this->expectException(InvalidCategoryIdException::class);

        CategoryId::fromString('invalid-id');
    }
}
