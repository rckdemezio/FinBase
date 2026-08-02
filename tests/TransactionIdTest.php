<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Domain\ValueObject;

use Demezio\Finbase\Finance\Domain\Exception\InvalidTransactionIdException;
use Demezio\Finbase\Finance\Domain\ValueObject\TransactionId;
use PHPUnit\Framework\TestCase;

final class TransactionIdTest extends TestCase
{
    public function testItGeneratesAValidVersionFourUuid(): void
    {
        $id = TransactionId::generate();

        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $id->value(),
        );
    }

    public function testItRestoresAndComparesIdentifiers(): void
    {
        $id = TransactionId::fromString('550E8400-E29B-41D4-A716-446655440000');

        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $id->value());
        self::assertTrue($id->equals(TransactionId::fromString('550e8400-e29b-41d4-a716-446655440000')));
    }

    public function testItRejectsAnInvalidIdentifier(): void
    {
        $this->expectException(InvalidTransactionIdException::class);

        TransactionId::fromString('invalid');
    }
}
