<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Domain\ValueObject;

use Demezio\Finbase\Finance\Domain\Exception\CurrencyMismatchException;
use Demezio\Finbase\Finance\Domain\Exception\InvalidCurrencyException;

/**
 * Representa um valor monetário em sua menor unidade, associado a uma moeda.
 */
final class Money
{
    private readonly int $amount;

    /**
     * @todo Extrair Currency quando o domínio exigir validação ISO 4217,
     *       símbolo, casas decimais ou formatação.
     */
    private readonly string $currency;

    public function __construct(int $amount, string $currency)
    {
        $currency = strtoupper(trim($currency));

        if ($currency === '') {
            throw new InvalidCurrencyException('A moeda não pode ser vazia.');
        }

        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * Retorna o valor na menor unidade da moeda.
     */
    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    /**
     * Retorna o código normalizado da moeda.
     */
    public function currencyCode(): string
    {
        return $this->currency;
    }

    /**
     * Soma dois valores da mesma moeda.
     *
     * @throws CurrencyMismatchException
     * @throws \OverflowException Caso o resultado não caiba em um inteiro.
     */
    public function add(self $other): self
    {
        $this->assertSameCurrency($other);

        if (
            ($other->amount > 0 && $this->amount > PHP_INT_MAX - $other->amount)
            || ($other->amount < 0 && $this->amount < PHP_INT_MIN - $other->amount)
        ) {
            throw new \OverflowException('A soma excede os limites de um inteiro.');
        }

        return new self($this->amount + $other->amount, $this->currency);
    }

    /**
     * Subtrai dois valores da mesma moeda.
     *
     * @throws CurrencyMismatchException
     * @throws \OverflowException Caso o resultado não caiba em um inteiro.
     */
    public function subtract(self $other): self
    {
        $this->assertSameCurrency($other);

        if (
            ($other->amount < 0 && $this->amount > PHP_INT_MAX + $other->amount)
            || ($other->amount > 0 && $this->amount < PHP_INT_MIN + $other->amount)
        ) {
            throw new \OverflowException('A subtração excede os limites de um inteiro.');
        }

        return new self($this->amount - $other->amount, $this->currency);
    }

    /**
     * Compara dois valores da mesma moeda.
     *
     * @return -1|0|1
     *
     * @throws CurrencyMismatchException
     */
    public function compare(self $other): int
    {
        $this->assertSameCurrency($other);

        return $this->amount <=> $other->amount;
    }

    /**
     * @throws CurrencyMismatchException
     */
    public function equals(self $other): bool
    {
        return $this->compare($other) === 0;
    }

    /**
     * @throws CurrencyMismatchException
     */
    public function isGreaterThan(self $other): bool
    {
        return $this->compare($other) > 0;
    }

    /**
     * @throws CurrencyMismatchException
     */
    public function isLessThan(self $other): bool
    {
        return $this->compare($other) < 0;
    }

    public function isPositive(): bool
    {
        return $this->amount > 0;
    }

    public function isNegative(): bool
    {
        return $this->amount < 0;
    }

    public function isZero(): bool
    {
        return $this->amount === 0;
    }

    /**
     * @throws CurrencyMismatchException
     */
    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new CurrencyMismatchException(
                sprintf('Não é possível operar entre %s e %s.', $this->currency, $other->currency)
            );
        }
    }
}
