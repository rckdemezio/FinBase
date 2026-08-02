<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Presentation\Http\Presenter;

use Demezio\Finbase\Finance\Application\Query\GetMonthlySummary\MonthlySummary;

final class MonthlySummaryPresenter
{
    /** @return array{period: string, income: array{amount: int, currency: string}, expenses: array{amount: int, currency: string}, result: array{amount: int, currency: string}, transaction_count: int} */
    public function present(MonthlySummary $summary): array
    {
        return [
            'period' => $summary->period(),
            'income' => ['amount' => $summary->income()->amount(), 'currency' => $summary->income()->currencyCode()],
            'expenses' => ['amount' => $summary->expenses()->amount(), 'currency' => $summary->expenses()->currencyCode()],
            'result' => ['amount' => $summary->result()->amount(), 'currency' => $summary->result()->currencyCode()],
            'transaction_count' => $summary->transactionCount(),
        ];
    }
}
