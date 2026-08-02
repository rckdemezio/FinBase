<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Domain\Exception;

/**
 * Indica um saldo incompatível com as invariantes da conta.
 */
final class InvalidAccountBalanceException extends \DomainException
{
}
