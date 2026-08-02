<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Application\UseCase\GetAccount;

use Demezio\Finbase\Finance\Application\Exception\AccountNotFoundException;
use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\Repository\AccountRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;

/**
 * Recupera uma conta financeira pelo seu identificador.
 */
final class GetAccount
{
    public function __construct(private readonly AccountRepository $repository)
    {
    }

    /**
     * @throws AccountNotFoundException
     */
    public function execute(AccountId $id): Account
    {
        $account = $this->repository->findById($id);

        if ($account === null) {
            throw new AccountNotFoundException('Conta não encontrada.');
        }

        return $account;
    }
}
