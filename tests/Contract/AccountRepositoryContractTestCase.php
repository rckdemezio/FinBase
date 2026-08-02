<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Contract;

use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\Repository\AccountRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

abstract class AccountRepositoryContractTestCase extends TestCase
{
    abstract protected function createRepository(): AccountRepository;

    public function testSavesAndFindsAccountByIdentifier(): void
    {
        $repository = $this->createRepository();
        $account = Account::open($this->accountId('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d'), 'Conta principal', 'BRL');

        $repository->save($account);

        $found = $repository->findById($account->id());

        self::assertNotNull($found);
        self::assertTrue($account->equals($found));
        self::assertSame('Conta principal', $found->name());
        self::assertTrue($found->balance()->equals(new Money(0, 'BRL')));
    }

    public function testReturnsNullWhenAccountIsNotFound(): void
    {
        $repository = $this->createRepository();

        $account = $repository->findById($this->accountId('2a8e5d4c-7041-4d49-9310-c9a389c4cc9f'));

        self::assertNull($account);
    }

    public function testUpdatesAccountWithSameIdentifier(): void
    {
        $repository = $this->createRepository();
        $id = $this->accountId('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d');

        $repository->save(Account::open($id, 'Conta antiga', 'BRL'));
        $repository->save(Account::restore($id, 'Conta atualizada', new Money(1200, 'BRL')));

        $found = $repository->findById($id);

        self::assertNotNull($found);
        self::assertSame('Conta atualizada', $found->name());
        self::assertTrue($found->balance()->equals(new Money(1200, 'BRL')));
        self::assertCount(1, $repository->all());
    }

    public function testReturnsAllSavedAccounts(): void
    {
        $repository = $this->createRepository();
        $first = Account::open($this->accountId('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d'), 'Conta principal', 'BRL');
        $second = Account::open($this->accountId('2a8e5d4c-7041-4d49-9310-c9a389c4cc9f'), 'Reserva', 'BRL');

        $repository->save($first);
        $repository->save($second);

        $accounts = $repository->all();
        $identifiers = array_map(static fn (Account $account): string => $account->id()->value(), $accounts);

        self::assertCount(2, $accounts);
        self::assertContains($first->id()->value(), $identifiers);
        self::assertContains($second->id()->value(), $identifiers);
    }

    private function accountId(string $value): AccountId
    {
        return AccountId::fromString($value);
    }
}
