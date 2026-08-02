<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Domain\Enum;

/**
 * Define a direção de uma movimentação financeira.
 */
enum TransactionType: string
{
    case CREDIT = 'CREDIT';
    case DEBIT = 'DEBIT';
}
