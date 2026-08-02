<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Domain\Entity;

use Demezio\Finbase\Finance\Domain\Enum\TransactionType;
use Demezio\Finbase\Finance\Domain\Exception\InvalidTransactionAmountException;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use Demezio\Finbase\Finance\Domain\ValueObject\TransactionId;

/**
 * Representa o registro imutável de uma movimentação financeira.
 */
final class Transaction
{
    private function __construct(
        private readonly TransactionId $id,
        private readonly AccountId $accountId,
        private readonly TransactionType $type,
        private readonly Money $amount,
        private readonly string $description,
        private readonly \DateTimeImmutable $occurredAt,
    ) {
    }

    /**
     * @throws InvalidTransactionAmountException
     */
    public static function record(
        TransactionId $id,
        AccountId $accountId,
        TransactionType $type,
        Money $amount,
        string $description,
        \DateTimeImmutable $occurredAt,
    ): self {
        self::assertPositiveAmount($amount);

        return new self($id, $accountId, $type, $amount, $description, $occurredAt);
    }

    /**
     * @throws InvalidTransactionAmountException
     */
    public static function restore(
        TransactionId $id,
        AccountId $accountId,
        TransactionType $type,
        Money $amount,
        string $description,
        \DateTimeImmutable $occurredAt,
    ): self {
        self::assertPositiveAmount($amount);

        return new self($id, $accountId, $type, $amount, $description, $occurredAt);
    }

    public function id(): TransactionId
    {
        return $this->id;
    }

    public function accountId(): AccountId
    {
        return $this->accountId;
    }

    public function type(): TransactionType
    {
        return $this->type;
    }

    public function amount(): Money
    {
        return $this->amount;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function occurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }

    /**
     * @throws InvalidTransactionAmountException
     */
    private static function assertPositiveAmount(Money $amount): void
    {
        if (! $amount->isPositive()) {
            throw new InvalidTransactionAmountException('O valor da transação deve ser positivo.');
        }
    }
}
