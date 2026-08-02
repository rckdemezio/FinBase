<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Presentation\Http\Presenter;

use Demezio\Finbase\Finance\Domain\Entity\Transaction;

final class TransactionPresenter
{
    /** @return array{id: string, account_id: string, type: string, amount: array{value: int, currency: string}, description: string, occurred_at: string} */
    public function present(Transaction $transaction): array
    {
        return [
            'id' => $transaction->id()->value(),
            'account_id' => $transaction->accountId()->value(),
            'type' => $transaction->type()->value,
            'amount' => ['value' => $transaction->amount()->amount(), 'currency' => $transaction->amount()->currencyCode()],
            'description' => $transaction->description(),
            'occurred_at' => $transaction->occurredAt()->format(DATE_ATOM),
        ];
    }
}
