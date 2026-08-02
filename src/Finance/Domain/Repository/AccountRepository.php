<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Domain\Repository;

use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;

/**
 * Define a persistência de contas no domínio financeiro.
 */
interface AccountRepository
{
    public function save(Account $account): void;

    public function findById(AccountId $id): ?Account;

    /**
     * @return list<Account>
     */
    public function all(): array;
}
