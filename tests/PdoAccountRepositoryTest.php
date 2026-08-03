<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Infrastructure\Persistence\Pdo;

use Demezio\Finbase\Finance\Domain\Repository\AccountRepository;
use Demezio\Finbase\Finance\Infrastructure\Database\PdoConnectionFactory;
use Demezio\Finbase\Finance\Infrastructure\Persistence\Pdo\PdoAccountRepository;
use Demezio\Finbase\Tests\Contract\AccountRepositoryContractTestCase;

final class PdoAccountRepositoryTest extends AccountRepositoryContractTestCase
{
    private \PDO $connection;

    protected function setUp(): void
    {
        /** @var array{driver: string, host: string, port: int, database: string, username: string, password: string} $config */
        $config = require dirname(__DIR__).'/config/database.php';
        $this->connection = PdoConnectionFactory::create($config);
        $this->connection->exec('DELETE FROM transactions');
        $this->connection->exec('DELETE FROM accounts');
    }

    protected function createRepository(): AccountRepository
    {
        return new PdoAccountRepository($this->connection);
    }
}
