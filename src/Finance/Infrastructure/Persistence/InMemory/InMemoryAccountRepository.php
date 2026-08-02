<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory;

use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\Repository\AccountRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;

/**
 * Armazena contas em memória durante o ciclo de vida do processo atual.
 */
final class InMemoryAccountRepository implements AccountRepository
{
    /** @var array<string, Account> */
    private array $accounts = [];

    public function save(Account $account): void
    {
        $this->accounts[$account->id()->value()] = $account;
    }

    public function findById(AccountId $id): ?Account
    {
        return $this->accounts[$id->value()] ?? null;
    }

    /**
     * @return list<Account>
     */
    public function all(): array
    {
        return array_values($this->accounts);
    }
}
