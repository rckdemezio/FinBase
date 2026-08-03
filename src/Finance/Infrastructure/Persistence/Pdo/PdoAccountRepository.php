<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Infrastructure\Persistence\Pdo;

use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\Repository\AccountRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use Demezio\Finbase\Finance\Infrastructure\Exception\PersistenceException;

/**
 * Persiste contas em um banco relacional através de PDO.
 */
final class PdoAccountRepository implements AccountRepository
{
    public function __construct(private readonly \PDO $connection)
    {
    }

    public function save(Account $account): void
    {
        try {
            $statement = $this->connection->prepare(
                'INSERT INTO accounts (id, name, balance_amount, currency)
                 VALUES (:id, :name, :balance_amount, :currency)
                 ON DUPLICATE KEY UPDATE
                    name = VALUES(name),
                    balance_amount = VALUES(balance_amount),
                    currency = VALUES(currency)',
            );
            $statement->execute([
                'id' => $account->id()->value(),
                'name' => $account->name(),
                'balance_amount' => $account->balance()->amount(),
                'currency' => $account->balance()->currencyCode(),
            ]);
        } catch (\PDOException $exception) {
            throw new PersistenceException('Não foi possível salvar a conta no banco de dados.', previous: $exception);
        }
    }

    public function findById(AccountId $id): ?Account
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT id, name, balance_amount, currency FROM accounts WHERE id = :id',
            );
            $statement->execute(['id' => $id->value()]);
            $record = $statement->fetch();
        } catch (\PDOException $exception) {
            throw new PersistenceException('Não foi possível buscar a conta no banco de dados.', previous: $exception);
        }

        return $record === false ? null : $this->deserialize($record);
    }

    /**
     * @return list<Account>
     */
    public function all(): array
    {
        try {
            $records = $this->connection
                ->query('SELECT id, name, balance_amount, currency FROM accounts ORDER BY id')
                ->fetchAll();
        } catch (\PDOException $exception) {
            throw new PersistenceException('Não foi possível listar as contas no banco de dados.', previous: $exception);
        }

        return array_map(fn (array $record): Account => $this->deserialize($record), $records);
    }

    /**
     * @param array{id: string, name: string, balance_amount: int, currency: string} $record
     */
    private function deserialize(array $record): Account
    {
        try {
            return Account::restore(
                AccountId::fromString($record['id']),
                $record['name'],
                new Money($record['balance_amount'], $record['currency']),
            );
        } catch (\Throwable $exception) {
            throw new PersistenceException('O banco de dados contém uma conta inválida.', previous: $exception);
        }
    }
}
