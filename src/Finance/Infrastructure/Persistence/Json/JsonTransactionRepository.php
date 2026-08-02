<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Infrastructure\Persistence\Json;

use Demezio\Finbase\Finance\Domain\Entity\Transaction;
use Demezio\Finbase\Finance\Domain\Enum\TransactionType;
use Demezio\Finbase\Finance\Domain\Repository\TransactionRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use Demezio\Finbase\Finance\Domain\ValueObject\TransactionId;
use Demezio\Finbase\Finance\Infrastructure\Exception\PersistenceException;

/**
 * Persiste transações como uma lista de dados JSON em um arquivo local.
 */
final class JsonTransactionRepository implements TransactionRepository
{
    public function __construct(private readonly string $filePath)
    {
    }

    public function save(Transaction $transaction): void
    {
        $transactions = $this->read();
        $transactions[$transaction->id()->value()] = $transaction;

        try {
            $json = json_encode(
                array_values(array_map($this->serialize(...), $transactions)),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
            );
        } catch (\JsonException $exception) {
            throw new PersistenceException('Não foi possível codificar as transações em JSON.', previous: $exception);
        }

        if (@file_put_contents($this->filePath, $json, LOCK_EX) === false) {
            throw new PersistenceException(sprintf('Não foi possível gravar o arquivo "%s".', $this->filePath));
        }
    }

    public function findById(TransactionId $id): ?Transaction
    {
        return $this->read()[$id->value()] ?? null;
    }

    /**
     * @return list<Transaction>
     */
    public function findByAccountId(AccountId $accountId): array
    {
        return array_values(array_filter(
            $this->read(),
            static fn (Transaction $transaction): bool => $transaction->accountId()->equals($accountId),
        ));
    }

    /**
     * @return list<Transaction>
     */
    public function all(): array
    {
        return array_values($this->read());
    }

    /**
     * @return array<string, Transaction>
     */
    private function read(): array
    {
        if (! file_exists($this->filePath)) {
            return [];
        }

        $json = @file_get_contents($this->filePath);

        if ($json === false) {
            throw new PersistenceException(sprintf('Não foi possível ler o arquivo "%s".', $this->filePath));
        }

        try {
            $records = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new PersistenceException('O arquivo de transações contém JSON inválido.', previous: $exception);
        }

        if (! is_array($records) || ! array_is_list($records)) {
            throw new PersistenceException('O arquivo de transações deve conter uma lista JSON.');
        }

        $transactions = [];

        foreach ($records as $record) {
            $transaction = $this->deserialize($record);
            $transactions[$transaction->id()->value()] = $transaction;
        }

        return $transactions;
    }

    /**
     * @return array{id: string, account_id: string, type: string, amount: array{value: int, currency: string}, description: string, occurred_at: string}
     */
    private function serialize(Transaction $transaction): array
    {
        return [
            'id' => $transaction->id()->value(),
            'account_id' => $transaction->accountId()->value(),
            'type' => $transaction->type()->value,
            'amount' => [
                'value' => $transaction->amount()->amount(),
                'currency' => $transaction->amount()->currencyCode(),
            ],
            'description' => $transaction->description(),
            'occurred_at' => $transaction->occurredAt()->format(DATE_ATOM),
        ];
    }

    private function deserialize(mixed $record): Transaction
    {
        if (
            ! is_array($record)
            || ! is_string($record['id'] ?? null)
            || ! is_string($record['account_id'] ?? null)
            || ! is_string($record['type'] ?? null)
            || ! is_array($record['amount'] ?? null)
            || ! is_int($record['amount']['value'] ?? null)
            || ! is_string($record['amount']['currency'] ?? null)
            || ! is_string($record['description'] ?? null)
            || ! is_string($record['occurred_at'] ?? null)
        ) {
            throw new PersistenceException('O arquivo de transações contém um registro inválido.');
        }

        try {
            return Transaction::restore(
                TransactionId::fromString($record['id']),
                AccountId::fromString($record['account_id']),
                TransactionType::from($record['type']),
                new Money($record['amount']['value'], $record['amount']['currency']),
                $record['description'],
                new \DateTimeImmutable($record['occurred_at']),
            );
        } catch (\Throwable $exception) {
            throw new PersistenceException('O arquivo de transações contém dados inválidos.', previous: $exception);
        }
    }
}
