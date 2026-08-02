<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Application\Query\GetMonthlySummary;

use Demezio\Finbase\Finance\Domain\ValueObject\Money;

/**
 * Representa a consolidação das transações de uma conta em um mês.
 */
final class MonthlySummary
{
    public function __construct(
        private readonly int $year,
        private readonly int $month,
        private readonly Money $income,
        private readonly Money $expenses,
        private readonly Money $result,
        private readonly int $transactionCount,
    ) {
    }

    public function period(): string
    {
        return sprintf('%04d-%02d', $this->year, $this->month);
    }

    public function income(): Money { return $this->income; }
    public function expenses(): Money { return $this->expenses; }
    public function result(): Money { return $this->result; }
    public function transactionCount(): int { return $this->transactionCount; }
}
