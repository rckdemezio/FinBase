<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Domain\Entity;

use Demezio\Finbase\Finance\Domain\Exception\InsufficientFundsException;
use Demezio\Finbase\Finance\Domain\Exception\InvalidAccountAmountException;
use Demezio\Finbase\Finance\Domain\Exception\InvalidAccountBalanceException;
use Demezio\Finbase\Finance\Domain\Exception\InvalidAccountNameException;
use Demezio\Finbase\Finance\Domain\Exception\CurrencyMismatchException;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;

/**
 * Representa um local onde valores monetários são mantidos.
 *
 * Nesta primeira versão, uma conta não permite saldo negativo.
 */
final class Account
{
    private function __construct(
        private readonly AccountId $id,
        private string $name,
        private Money $balance
    ) {
    }

    /**
     * Abre uma conta com saldo inicial zero.
     *
     * @throws InvalidAccountNameException
     */
    public static function open(AccountId $id, string $name, string $currency): self
    {
        return new self(
            $id,
            self::normalizeName($name),
            new Money(0, $currency)
        );
    }

    /**
     * Reconstitui uma conta previamente persistida.
     *
     * @throws InvalidAccountNameException
     * @throws InvalidAccountBalanceException Caso o saldo reconstituído seja negativo.
     */
    public static function restore(AccountId $id, string $name, Money $balance): self
    {
        if ($balance->isNegative()) {
            throw new InvalidAccountBalanceException('Uma conta não pode ser reconstituída com saldo negativo.');
        }

        return new self($id, self::normalizeName($name), $balance);
    }

    public function id(): AccountId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function balance(): Money
    {
        return $this->balance;
    }

    /**
     * @throws InvalidAccountAmountException
     * @throws CurrencyMismatchException
     * @throws \OverflowException Caso o novo saldo exceda os limites de um inteiro.
     */
    public function credit(Money $amount): void
    {
        $this->assertPositiveAmount($amount);

        $this->balance = $this->balance->add($amount);
    }

    /**
     * @throws InvalidAccountAmountException
     * @throws InsufficientFundsException
     * @throws CurrencyMismatchException
     */
    public function debit(Money $amount): void
    {
        $this->assertPositiveAmount($amount);

        $balance = $this->balance->subtract($amount);

        if ($balance->isNegative()) {
            throw new InsufficientFundsException('A conta não possui saldo suficiente para este débito.');
        }

        $this->balance = $balance;
    }

    /**
     * @throws InvalidAccountNameException
     */
    public function rename(string $name): void
    {
        $this->name = self::normalizeName($name);
    }

    public function equals(self $other): bool
    {
        return $this->id->equals($other->id);
    }

    /**
     * @throws InvalidAccountAmountException
     */
    private function assertPositiveAmount(Money $amount): void
    {
        if (! $amount->isPositive()) {
            throw new InvalidAccountAmountException('O valor da operação deve ser positivo.');
        }
    }

    /**
     * @throws InvalidAccountNameException
     */
    private static function normalizeName(string $name): string
    {
        $name = trim($name);

        if ($name === '') {
            throw new InvalidAccountNameException('O nome da conta não pode ser vazio.');
        }

        return $name;
    }
}
