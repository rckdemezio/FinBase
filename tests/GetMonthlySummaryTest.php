<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Application\Query\GetMonthlySummary;

use Demezio\Finbase\Finance\Application\Query\GetMonthlySummary\GetMonthlySummary;
use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\Entity\Transaction;
use Demezio\Finbase\Finance\Domain\Enum\TransactionType;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use Demezio\Finbase\Finance\Domain\ValueObject\TransactionId;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryAccountRepository;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryTransactionRepository;
use PHPUnit\Framework\TestCase;

final class GetMonthlySummaryTest extends TestCase
{
    public function testItConsolidatesOnlyTransactionsFromTheRequestedMonth(): void
    {
        $accounts = new InMemoryAccountRepository();
        $transactions = new InMemoryTransactionRepository();
        $account = Account::open($this->accountId(), 'Principal', 'BRL');
        $accounts->save($account);
        $transactions->save($this->transaction(TransactionType::CREDIT, 500000, '2026-08-01'));
        $transactions->save($this->transaction(TransactionType::DEBIT, 242000, '2026-08-02'));
        $transactions->save($this->transaction(TransactionType::DEBIT, 1000, '2026-07-31'));

        $summary = (new GetMonthlySummary($accounts, $transactions))->execute($account->id(), 2026, 8);

        self::assertSame('2026-08', $summary->period());
        self::assertTrue($summary->income()->equals(new Money(500000, 'BRL')));
        self::assertTrue($summary->expenses()->equals(new Money(242000, 'BRL')));
        self::assertTrue($summary->result()->equals(new Money(258000, 'BRL')));
        self::assertSame(2, $summary->transactionCount());
    }

    private function transaction(TransactionType $type, int $amount, string $date): Transaction
    {
        return Transaction::record(TransactionId::generate(), $this->accountId(), $type, new Money($amount, 'BRL'), '', new \DateTimeImmutable($date));
    }

    private function accountId(): AccountId
    {
        return AccountId::fromString('550e8400-e29b-41d4-a716-446655440000');
    }
}
