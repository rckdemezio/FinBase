<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Domain\Exception;

/**
 * Indica que um débito violaria a regra de saldo não negativo da conta.
 */
final class InsufficientFundsException extends \DomainException
{
}
