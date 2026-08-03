<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Infrastructure\Persistence\Pdo;

use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\Repository\TransactionRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Infrastructure\Database\PdoConnectionFactory;
use Demezio\Finbase\Finance\Infrastructure\Persistence\Pdo\PdoAccountRepository;
use Demezio\Finbase\Finance\Infrastructure\Persistence\Pdo\PdoTransactionRepository;
use Demezio\Finbase\Tests\Contract\TransactionRepositoryContractTestCase;

final class PdoTransactionRepositoryTest extends TransactionRepositoryContractTestCase
{
    private \PDO $connection;

    protected function setUp(): void
    {
        /** @var array{driver: string, host: string, port: int, database: string, username: string, password: string} $config */
        $config = require dirname(__DIR__).'/config/database.php';
        $this->connection = PdoConnectionFactory::create($config);
        $this->connection->exec('DELETE FROM transactions');
        $this->connection->exec('DELETE FROM accounts');

        $accounts = new PdoAccountRepository($this->connection);
        $accounts->save(Account::open($this->accountId('d9428888-122b-11e1-b85c-61cd3cbb3210'), 'Conta principal', 'BRL'));
        $accounts->save(Account::open($this->accountId('6ba7b810-9dad-11d1-80b4-00c04fd430c8'), 'Conta secundária', 'BRL'));
    }

    protected function createRepository(): TransactionRepository
    {
        return new PdoTransactionRepository($this->connection);
    }

    private function accountId(string $value): AccountId
    {
        return AccountId::fromString($value);
    }
}
