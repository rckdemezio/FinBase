<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Application\UseCase\ListTransactions;

use Demezio\Finbase\Finance\Application\Exception\AccountNotFoundException;
use Demezio\Finbase\Finance\Domain\Entity\Transaction;
use Demezio\Finbase\Finance\Domain\Repository\AccountRepository;
use Demezio\Finbase\Finance\Domain\Repository\TransactionRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;

final class ListTransactions
{
    public function __construct(
        private readonly AccountRepository $accounts,
        private readonly TransactionRepository $transactions,
    ) {
    }

    /**
     * @return list<Transaction>
     *
     * @throws AccountNotFoundException
     */
    public function execute(AccountId $accountId): array
    {
        if ($this->accounts->findById($accountId) === null) {
            throw new AccountNotFoundException(sprintf('A conta "%s" não foi encontrada.', $accountId));
        }

        return $this->transactions->findByAccountId($accountId);
    }
}
