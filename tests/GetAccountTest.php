<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Application\UseCase\GetAccount;

use Demezio\Finbase\Finance\Application\Exception\AccountNotFoundException;
use Demezio\Finbase\Finance\Application\UseCase\GetAccount\GetAccount;
use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryAccountRepository;
use PHPUnit\Framework\TestCase;

final class GetAccountTest extends TestCase
{
    public function testItReturnsTheAccountWithTheRequestedIdentifier(): void
    {
        $repository = new InMemoryAccountRepository();
        $account = Account::open($this->id(), 'Conta Principal', 'BRL');
        $repository->save($account);

        $found = (new GetAccount($repository))->execute($this->id());

        self::assertSame($account, $found);
    }

    public function testItThrowsWhenTheAccountDoesNotExist(): void
    {
        $this->expectException(AccountNotFoundException::class);

        (new GetAccount(new InMemoryAccountRepository()))->execute($this->id());
    }

    private function id(): AccountId
    {
        return AccountId::fromString('550e8400-e29b-41d4-a716-446655440000');
    }
}
