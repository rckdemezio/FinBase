<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Infrastructure\Persistence\Json;

use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\Repository\AccountRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use Demezio\Finbase\Finance\Infrastructure\Exception\PersistenceException;

/**
 * Persiste contas como uma lista de dados JSON em um arquivo local.
 */
final class JsonAccountRepository implements AccountRepository
{
    public function __construct(private readonly string $filePath)
    {
    }

    public function save(Account $account): void
    {
        $accounts = $this->read();
        $accounts[$account->id()->value()] = $account;

        $records = array_map(
            fn (Account $storedAccount): array => $this->serialize($storedAccount),
            $accounts,
        );

        try {
            $json = json_encode(
                array_values($records),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
            );
        } catch (\JsonException $exception) {
            throw new PersistenceException('Não foi possível codificar as contas em JSON.', previous: $exception);
        }

        if (@file_put_contents($this->filePath, $json, LOCK_EX) === false) {
            throw new PersistenceException(sprintf('Não foi possível gravar o arquivo "%s".', $this->filePath));
        }
    }

    public function findById(AccountId $id): ?Account
    {
        return $this->read()[$id->value()] ?? null;
    }

    /**
     * @return list<Account>
     */
    public function all(): array
    {
        return array_values($this->read());
    }

    /**
     * @return array<string, Account>
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
            throw new PersistenceException('O arquivo de contas contém JSON inválido.', previous: $exception);
        }

        if (! is_array($records) || ! array_is_list($records)) {
            throw new PersistenceException('O arquivo de contas deve conter uma lista JSON.');
        }

        $accounts = [];

        foreach ($records as $record) {
            $account = $this->deserialize($record);
            $accounts[$account->id()->value()] = $account;
        }

        return $accounts;
    }

    /**
     * @return array{id: string, name: string, balance: array{amount: int, currency: string}}
     */
    private function serialize(Account $account): array
    {
        return [
            'id' => $account->id()->value(),
            'name' => $account->name(),
            'balance' => [
                'amount' => $account->balance()->amount(),
                'currency' => $account->balance()->currency(),
            ],
        ];
    }

    private function deserialize(mixed $record): Account
    {
        if (
            ! is_array($record)
            || ! is_string($record['id'] ?? null)
            || ! is_string($record['name'] ?? null)
            || ! is_array($record['balance'] ?? null)
            || ! is_int($record['balance']['amount'] ?? null)
            || ! is_string($record['balance']['currency'] ?? null)
        ) {
            throw new PersistenceException('O arquivo de contas contém um registro inválido.');
        }

        try {
            return Account::restore(
                AccountId::fromString($record['id']),
                $record['name'],
                new Money($record['balance']['amount'], $record['balance']['currency']),
            );
        } catch (\Throwable $exception) {
            throw new PersistenceException('O arquivo de contas contém dados inválidos.', previous: $exception);
        }
    }
}
