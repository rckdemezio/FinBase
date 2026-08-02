<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Application\UseCase\DebitAccount;

use Demezio\Finbase\Finance\Application\Exception\AccountNotFoundException;
use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\Repository\AccountRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;

/**
 * Debita um valor de uma conta existente.
 */
final class DebitAccount
{
    public function __construct(private readonly AccountRepository $repository)
    {
    }

    /**
     * @throws AccountNotFoundException
     */
    public function execute(AccountId $id, Money $amount): Account
    {
        $account = $this->repository->findById($id);

        if ($account === null) {
            throw new AccountNotFoundException(sprintf('A conta "%s" não foi encontrada.', $id));
        }

        $account->debit($amount);

        $this->repository->save($account);

        return $account;
    }
}
