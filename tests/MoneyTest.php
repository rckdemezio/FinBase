<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Domain\ValueObject;

use Demezio\Finbase\Finance\Domain\Exception\CurrencyMismatchException;
use Demezio\Finbase\Finance\Domain\Exception\InvalidCurrencyException;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function testCreatesPositiveMoney(): void
    {
        $money = new Money(1000, 'BRL');

        self::assertSame(1000, $money->amount());
        self::assertSame('BRL', $money->currency());
    }

    public function testCreatesNegativeMoney(): void
    {
        $money = new Money(-1000, 'BRL');

        self::assertSame(-1000, $money->amount());
        self::assertTrue($money->isNegative());
    }

    public function testCreatesZeroMoney(): void
    {
        $money = new Money(0, 'BRL');

        self::assertTrue($money->isZero());
        self::assertFalse($money->isPositive());
        self::assertFalse($money->isNegative());
    }

    public function testNormalizesCurrency(): void
    {
        $money = new Money(1000, ' brl ');

        self::assertSame('BRL', $money->currency());
    }

    public function testRejectsEmptyCurrency(): void
    {
        $this->expectException(InvalidCurrencyException::class);

        new Money(1000, '   ');
    }

    public function testReturnsNormalizedCurrencyCode(): void
    {
        $money = new Money(1000, 'brl');

        self::assertSame('BRL', $money->currencyCode());
    }

    public function testAddsMoneyWithSameCurrency(): void
    {
        $result = (new Money(1000, 'BRL'))->add(new Money(500, 'BRL'));

        self::assertSame(1500, $result->amount());
        self::assertSame('BRL', $result->currency());
    }

    public function testSubtractsMoneyWithSameCurrency(): void
    {
        $result = (new Money(1000, 'BRL'))->subtract(new Money(500, 'BRL'));

        self::assertSame(500, $result->amount());
    }

    public function testOperationsReturnNewInstanceWithoutChangingOriginalMoney(): void
    {
        $original = new Money(1000, 'BRL');
        $result = $original->add(new Money(500, 'BRL'));

        self::assertNotSame($original, $result);
        self::assertSame(1000, $original->amount());
        self::assertSame(1500, $result->amount());
    }

    public function testRejectsAdditionBetweenDifferentCurrencies(): void
    {
        $this->expectException(CurrencyMismatchException::class);

        (new Money(1000, 'BRL'))->add(new Money(500, 'USD'));
    }

    public function testRejectsSubtractionBetweenDifferentCurrencies(): void
    {
        $this->expectException(CurrencyMismatchException::class);

        (new Money(1000, 'BRL'))->subtract(new Money(500, 'USD'));
    }

    public function testComparesEqualMoneyByAmountAndCurrency(): void
    {
        self::assertTrue((new Money(1000, 'BRL'))->equals(new Money(1000, 'BRL')));
        self::assertFalse((new Money(1000, 'BRL'))->equals(new Money(999, 'BRL')));
    }

    public function testComparesMoneyValues(): void
    {
        $money = new Money(1000, 'BRL');

        self::assertSame(-1, $money->compare(new Money(1001, 'BRL')));
        self::assertSame(0, $money->compare(new Money(1000, 'BRL')));
        self::assertSame(1, $money->compare(new Money(999, 'BRL')));
        self::assertTrue($money->isGreaterThan(new Money(999, 'BRL')));
        self::assertTrue($money->isLessThan(new Money(1001, 'BRL')));
    }

    public function testRejectsComparisonBetweenDifferentCurrencies(): void
    {
        $this->expectException(CurrencyMismatchException::class);

        (new Money(1000, 'BRL'))->compare(new Money(1000, 'USD'));
    }

    public function testRejectsEqualityBetweenDifferentCurrencies(): void
    {
        $this->expectException(CurrencyMismatchException::class);

        (new Money(1000, 'BRL'))->equals(new Money(1000, 'USD'));
    }

    public function testRejectsSumThatOverflowsIntegerRange(): void
    {
        $this->expectException(\OverflowException::class);

        (new Money(PHP_INT_MAX, 'BRL'))->add(new Money(1, 'BRL'));
    }
}
