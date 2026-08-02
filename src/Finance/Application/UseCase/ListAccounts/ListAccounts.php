<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Application\UseCase\ListAccounts;

use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\Repository\AccountRepository;

/**
 * Lista as contas disponíveis na persistência.
 */
final class ListAccounts
{
    public function __construct(private readonly AccountRepository $repository)
    {
    }

    /**
     * @return list<Account>
     */
    public function execute(): array
    {
        return $this->repository->all();
    }
}
