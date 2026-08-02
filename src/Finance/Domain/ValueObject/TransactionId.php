<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Domain\ValueObject;

use Demezio\Finbase\Finance\Domain\Exception\InvalidTransactionIdException;

/**
 * Representa a identidade imutável de uma transação financeira.
 */
final class TransactionId
{
    private readonly string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Gera um identificador UUID na versão 4.
     */
    public static function generate(): self
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
        $value = bin2hex($bytes);

        return new self(sprintf(
            '%s-%s-%s-%s-%s',
            substr($value, 0, 8),
            substr($value, 8, 4),
            substr($value, 12, 4),
            substr($value, 16, 4),
            substr($value, 20, 12),
        ));
    }

    /**
     * @throws InvalidTransactionIdException
     */
    public static function fromString(string $value): self
    {
        $value = strtolower(trim($value));

        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/D', $value) !== 1) {
            throw new InvalidTransactionIdException('O identificador da transação deve ser um UUID válido.');
        }

        return new self($value);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
