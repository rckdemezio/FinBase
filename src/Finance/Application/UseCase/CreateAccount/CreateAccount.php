<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Application\UseCase\CreateAccount;

use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\Repository\AccountRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;

/**
 * Cria e persiste uma nova conta financeira.
 */
final class CreateAccount
{
    public function __construct(private readonly AccountRepository $repository)
    {
    }

    public function execute(string $name, string $currency): Account
    {
        $account = Account::open(AccountId::generate(), $name, $currency);

        $this->repository->save($account);

        return $account;
    }
}
