<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Application\UseCase\ListTransactions;

use Demezio\Finbase\Finance\Application\UseCase\ListTransactions\ListTransactions;
use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\Entity\Transaction;
use Demezio\Finbase\Finance\Domain\Enum\TransactionType;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use Demezio\Finbase\Finance\Domain\ValueObject\TransactionId;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryAccountRepository;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryTransactionRepository;
use PHPUnit\Framework\TestCase;

final class ListTransactionsTest extends TestCase
{
    public function testItReturnsOnlyTheTransactionsForTheAccount(): void
    {
        $accounts = new InMemoryAccountRepository();
        $transactions = new InMemoryTransactionRepository();
        $account = Account::open($this->accountId(), 'Principal', 'BRL');
        $accounts->save($account);
        $transaction = Transaction::record(TransactionId::generate(), $account->id(), TransactionType::CREDIT, new Money(5000, 'BRL'), '', new \DateTimeImmutable());
        $transactions->save($transaction);

        self::assertSame([$transaction], (new ListTransactions($accounts, $transactions))->execute($account->id()));
    }

    private function accountId(): AccountId
    {
        return AccountId::fromString('550e8400-e29b-41d4-a716-446655440000');
    }
}
