<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Application\UseCase\RecordIncome;

use Demezio\Finbase\Finance\Application\Exception\AccountNotFoundException;
use Demezio\Finbase\Finance\Application\UseCase\RecordIncome\RecordIncome;
use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\Enum\TransactionType;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryAccountRepository;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryTransactionRepository;
use PHPUnit\Framework\TestCase;

final class RecordIncomeTest extends TestCase
{
    public function testItIncreasesTheBalanceAndRecordsACreditTransaction(): void
    {
        $accounts = new InMemoryAccountRepository();
        $transactions = new InMemoryTransactionRepository();
        $account = Account::open($this->accountId(), 'Conta Principal', 'BRL');
        $accounts->save($account);

        $transaction = (new RecordIncome($accounts, $transactions))->execute(
            $account->id(),
            new Money(500000, 'BRL'),
            'Salário',
            new \DateTimeImmutable('2026-08-02 14:30:00-03:00'),
        );

        self::assertTrue($account->balance()->equals(new Money(500000, 'BRL')));
        self::assertSame(TransactionType::CREDIT, $transaction->type());
        self::assertTrue($transaction->accountId()->equals($account->id()));
        self::assertSame('Salário', $transaction->description());
        self::assertSame($transaction, $transactions->findById($transaction->id()));
    }

    public function testItDoesNotRecordATransactionWhenTheAccountDoesNotExist(): void
    {
        $transactions = new InMemoryTransactionRepository();

        $this->expectException(AccountNotFoundException::class);

        try {
            (new RecordIncome(new InMemoryAccountRepository(), $transactions))->execute(
                $this->accountId(),
                new Money(500000, 'BRL'),
                'Salário',
                new \DateTimeImmutable(),
            );
        } finally {
            self::assertSame([], $transactions->all());
        }
    }

    private function accountId(): AccountId
    {
        return AccountId::fromString('550e8400-e29b-41d4-a716-446655440000');
    }
}
