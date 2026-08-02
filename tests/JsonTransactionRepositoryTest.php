<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Infrastructure\Persistence\Json;

use Demezio\Finbase\Finance\Domain\Entity\Transaction;
use Demezio\Finbase\Finance\Domain\Enum\TransactionType;
use Demezio\Finbase\Finance\Domain\Repository\TransactionRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use Demezio\Finbase\Finance\Domain\ValueObject\TransactionId;
use Demezio\Finbase\Finance\Infrastructure\Exception\PersistenceException;
use Demezio\Finbase\Finance\Infrastructure\Persistence\Json\JsonTransactionRepository;
use Demezio\Finbase\Tests\Contract\TransactionRepositoryContractTestCase;

final class JsonTransactionRepositoryTest extends TransactionRepositoryContractTestCase
{
    private string $filePath;

    protected function setUp(): void
    {
        $this->filePath = sys_get_temp_dir().'/finbase-transactions-'.bin2hex(random_bytes(8)).'.json';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }

    protected function createRepository(): TransactionRepository
    {
        return new JsonTransactionRepository($this->filePath);
    }

    public function testItPersistsAStableTransactionRepresentation(): void
    {
        $transaction = Transaction::record(
            TransactionId::fromString('550e8400-e29b-41d4-a716-446655440000'),
            AccountId::fromString('d9428888-122b-11e1-b85c-61cd3cbb3210'),
            TransactionType::DEBIT,
            new Money(5000, 'BRL'),
            'Mercado',
            new \DateTimeImmutable('2026-08-02 14:30:00-03:00'),
        );

        $this->createRepository()->save($transaction);

        self::assertSame(
            [[
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'account_id' => 'd9428888-122b-11e1-b85c-61cd3cbb3210',
                'type' => 'DEBIT',
                'amount' => ['value' => 5000, 'currency' => 'BRL'],
                'description' => 'Mercado',
                'occurred_at' => '2026-08-02T14:30:00-03:00',
            ]],
            json_decode((string) file_get_contents($this->filePath), true, 512, JSON_THROW_ON_ERROR),
        );
    }

    public function testItThrowsAPredictableExceptionForInvalidJson(): void
    {
        file_put_contents($this->filePath, '{');

        $this->expectException(PersistenceException::class);

        $this->createRepository()->all();
    }
}
