<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Domain\ValueObject;

use Demezio\Finbase\Finance\Domain\Exception\InvalidAccountIdException;

/**
 * Representa a identidade imutável de uma conta financeira.
 */
final class AccountId
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
        return new self(self::generateUuidV4());
    }

    /**
     * Reconstitui um identificador a partir de sua representação UUID.
     *
     * @throws InvalidAccountIdException
     */
    public static function fromString(string $value): self
    {
        $value = strtolower(trim($value));

        if (! self::isValidUuid($value)) {
            throw new InvalidAccountIdException('O identificador da conta deve ser um UUID válido.');
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

    private static function generateUuidV4(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
        $value = bin2hex($bytes);

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($value, 0, 8),
            substr($value, 8, 4),
            substr($value, 12, 4),
            substr($value, 16, 4),
            substr($value, 20, 12)
        );
    }

    private static function isValidUuid(string $value): bool
    {
        return preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/D',
            $value
        ) === 1;
    }
}
