<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Application\UseCase\ListAccounts;

use Demezio\Finbase\Finance\Application\UseCase\ListAccounts\ListAccounts;
use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryAccountRepository;
use PHPUnit\Framework\TestCase;

final class ListAccountsTest extends TestCase
{
    public function testItReturnsAllAccountsFromTheRepository(): void
    {
        $repository = new InMemoryAccountRepository();
        $first = Account::open(AccountId::fromString('550e8400-e29b-41d4-a716-446655440000'), 'Principal', 'BRL');
        $second = Account::open(AccountId::fromString('9a5f8d6b-5c49-4c55-8d45-4e12ae41118d'), 'Reserva', 'BRL');
        $repository->save($first);
        $repository->save($second);

        self::assertSame([$first, $second], (new ListAccounts($repository))->execute());
    }
}
