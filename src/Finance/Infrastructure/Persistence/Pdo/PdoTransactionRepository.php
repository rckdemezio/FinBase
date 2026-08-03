<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Infrastructure\Persistence\Pdo;

use Demezio\Finbase\Finance\Domain\Entity\Transaction;
use Demezio\Finbase\Finance\Domain\Enum\TransactionType;
use Demezio\Finbase\Finance\Domain\Repository\TransactionRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use Demezio\Finbase\Finance\Domain\ValueObject\TransactionId;
use Demezio\Finbase\Finance\Infrastructure\Exception\PersistenceException;

/**
 * Persiste transações em um banco relacional através de PDO.
 *
 * Nesta primeira versão, occurred_at guarda a data e hora local da aplicação
 * no formato MySQL, sem converter nem persistir um offset de timezone.
 */
final class PdoTransactionRepository implements TransactionRepository
{
    public function __construct(private readonly \PDO $connection)
    {
    }

    public function save(Transaction $transaction): void
    {
        try {
            $statement = $this->connection->prepare(
                'INSERT INTO transactions (id, account_id, type, amount, currency, description, occurred_at)
                 VALUES (:id, :account_id, :type, :amount, :currency, :description, :occurred_at)
                 ON DUPLICATE KEY UPDATE
                    account_id = VALUES(account_id),
                    type = VALUES(type),
                    amount = VALUES(amount),
                    currency = VALUES(currency),
                    description = VALUES(description),
                    occurred_at = VALUES(occurred_at)',
            );
            $statement->execute([
                'id' => $transaction->id()->value(),
                'account_id' => $transaction->accountId()->value(),
                'type' => $transaction->type()->value,
                'amount' => $transaction->amount()->amount(),
                'currency' => $transaction->amount()->currencyCode(),
                'description' => $transaction->description(),
                'occurred_at' => $transaction->occurredAt()->format('Y-m-d H:i:s'),
            ]);
        } catch (\PDOException $exception) {
            throw new PersistenceException('Não foi possível salvar a transação no banco de dados.', previous: $exception);
        }
    }

    public function findById(TransactionId $id): ?Transaction
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT id, account_id, type, amount, currency, description, occurred_at
                 FROM transactions
                 WHERE id = :id',
            );
            $statement->execute(['id' => $id->value()]);
            $record = $statement->fetch();
        } catch (\PDOException $exception) {
            throw new PersistenceException('Não foi possível buscar a transação no banco de dados.', previous: $exception);
        }

        return $record === false ? null : $this->deserialize($record);
    }

    /**
     * @return list<Transaction>
     */
    public function findByAccountId(AccountId $accountId): array
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT id, account_id, type, amount, currency, description, occurred_at
                 FROM transactions
                 WHERE account_id = :account_id
                 ORDER BY id',
            );
            $statement->execute(['account_id' => $accountId->value()]);
            $records = $statement->fetchAll();
        } catch (\PDOException $exception) {
            throw new PersistenceException('Não foi possível buscar as transações da conta no banco de dados.', previous: $exception);
        }

        return array_map(fn (array $record): Transaction => $this->deserialize($record), $records);
    }

    /**
     * @return list<Transaction>
     */
    public function all(): array
    {
        try {
            $records = $this->connection
                ->query('SELECT id, account_id, type, amount, currency, description, occurred_at FROM transactions ORDER BY id')
                ->fetchAll();
        } catch (\PDOException $exception) {
            throw new PersistenceException('Não foi possível listar as transações no banco de dados.', previous: $exception);
        }

        return array_map(fn (array $record): Transaction => $this->deserialize($record), $records);
    }

    /**
     * @param array{id: string, account_id: string, type: string, amount: int, currency: string, description: string, occurred_at: string} $record
     */
    private function deserialize(array $record): Transaction
    {
        try {
            return Transaction::restore(
                TransactionId::fromString($record['id']),
                AccountId::fromString($record['account_id']),
                TransactionType::from($record['type']),
                new Money($record['amount'], $record['currency']),
                $record['description'],
                new \DateTimeImmutable($record['occurred_at']),
            );
        } catch (\Throwable $exception) {
            throw new PersistenceException('O banco de dados contém uma transação inválida.', previous: $exception);
        }
    }
}
