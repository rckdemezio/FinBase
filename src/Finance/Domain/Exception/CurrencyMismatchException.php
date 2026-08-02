<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Domain\Exception;

/**
 * Indica uma operação entre valores de moedas diferentes.
 */
final class CurrencyMismatchException extends \DomainException
{
}
