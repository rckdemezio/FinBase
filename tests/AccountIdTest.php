<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Domain\ValueObject;

use Demezio\Finbase\Finance\Domain\Exception\InvalidAccountIdException;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use PHPUnit\Framework\TestCase;

final class AccountIdTest extends TestCase
{
    public function testGeneratesDifferentUuidV4Identifiers(): void
    {
        $first = AccountId::generate();
        $second = AccountId::generate();

        self::assertNotSame($first->value(), $second->value());
        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/D',
            $first->value()
        );
    }

    public function testCreatesIdentifierFromValidString(): void
    {
        $identifier = AccountId::fromString('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d');

        self::assertSame('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d', $identifier->value());
    }

    public function testRejectsInvalidIdentifierString(): void
    {
        $this->expectException(InvalidAccountIdException::class);

        AccountId::fromString('invalid-id');
    }

    public function testComparesIdentifiersByValue(): void
    {
        $value = '9a5f8d6b-5c49-4c55-8d45-4e12ae41118d';

        $first = AccountId::fromString($value);
        $same = AccountId::fromString($value);
        $different = AccountId::fromString('2a8e5d4c-7041-4d49-9310-c9a389c4cc9f');

        self::assertTrue($first->equals($same));
        self::assertFalse($first->equals($different));
    }

    public function testConvertsIdentifierToString(): void
    {
        $identifier = AccountId::fromString('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d');

        self::assertSame($identifier->value(), (string) $identifier);
    }
}
