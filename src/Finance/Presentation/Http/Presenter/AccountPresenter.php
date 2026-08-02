<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Presentation\Http\Presenter;

use Demezio\Finbase\Finance\Domain\Entity\Account;

/**
 * Converte contas do domínio na representação exposta pela API HTTP.
 */
final class AccountPresenter
{
    /**
     * @return array{id: string, name: string, balance: array{amount: int, currency: string}}
     */
    public function present(Account $account): array
    {
        return [
            'id' => $account->id()->value(),
            'name' => $account->name(),
            'balance' => [
                'amount' => $account->balance()->amount(),
                'currency' => $account->balance()->currencyCode(),
            ],
        ];
    }
}
