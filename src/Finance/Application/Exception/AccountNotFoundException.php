<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Application\Exception;

/**
 * Indica que uma conta necessária para executar um caso de uso não foi encontrada.
 */
final class AccountNotFoundException extends \RuntimeException
{
}
