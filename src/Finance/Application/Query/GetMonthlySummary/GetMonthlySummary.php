<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Application\Query\GetMonthlySummary;

use Demezio\Finbase\Finance\Application\Exception\AccountNotFoundException;
use Demezio\Finbase\Finance\Domain\Enum\TransactionType;
use Demezio\Finbase\Finance\Domain\Repository\AccountRepository;
use Demezio\Finbase\Finance\Domain\Repository\TransactionRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;

final class GetMonthlySummary
{
    public function __construct(
        private readonly AccountRepository $accounts,
        private readonly TransactionRepository $transactions,
    ) {
    }

    /** @throws AccountNotFoundException */
    public function execute(AccountId $accountId, int $year, int $month): MonthlySummary
    {
        if ($month < 1 || $month > 12) {
            throw new \InvalidArgumentException('O mês deve estar entre 1 e 12.');
        }

        $account = $this->accounts->findById($accountId);

        if ($account === null) {
            throw new AccountNotFoundException(sprintf('A conta "%s" não foi encontrada.', $accountId));
        }

        $currency = $account->balance()->currencyCode();
        $income = new Money(0, $currency);
        $expenses = new Money(0, $currency);
        $count = 0;

        foreach ($this->transactions->findByAccountId($accountId) as $transaction) {
            if ((int) $transaction->occurredAt()->format('Y') !== $year || (int) $transaction->occurredAt()->format('n') !== $month) {
                continue;
            }

            $count++;
            $income = $transaction->type() === TransactionType::CREDIT
                ? $income->add($transaction->amount())
                : $income;
            $expenses = $transaction->type() === TransactionType::DEBIT
                ? $expenses->add($transaction->amount())
                : $expenses;
        }

        return new MonthlySummary($year, $month, $income, $expenses, $income->subtract($expenses), $count);
    }
}
