<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Application\UseCase\CreditAccount;

use Demezio\Finbase\Finance\Application\Exception\AccountNotFoundException;
use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\Repository\AccountRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;

/**
 * Credita um valor em uma conta existente.
 */
final class CreditAccount
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

        $account->credit($amount);

        $this->repository->save($account);

        return $account;
    }
}
