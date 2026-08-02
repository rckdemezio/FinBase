<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Application\UseCase\RecordExpense;

use Demezio\Finbase\Finance\Application\Exception\AccountNotFoundException;
use Demezio\Finbase\Finance\Domain\Entity\Transaction;
use Demezio\Finbase\Finance\Domain\Enum\TransactionType;
use Demezio\Finbase\Finance\Domain\Repository\AccountRepository;
use Demezio\Finbase\Finance\Domain\Repository\TransactionRepository;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use Demezio\Finbase\Finance\Domain\ValueObject\TransactionId;

/**
 * Registra uma saída de valor e atualiza o saldo da conta correspondente.
 *
 * A persistência da conta e da transação ocorre sequencialmente. Com
 * repositórios JSON, uma falha ao salvar a transação pode deixar o saldo salvo
 * sem o respectivo histórico; essa limitação será resolvida por uma
 * infraestrutura transacional futura.
 */
final class RecordExpense
{
    public function __construct(
        private readonly AccountRepository $accounts,
        private readonly TransactionRepository $transactions,
    ) {
    }

    /**
     * @throws AccountNotFoundException
     */
    public function execute(AccountId $accountId, Money $amount, string $description, \DateTimeImmutable $occurredAt): Transaction
    {
        $account = $this->accounts->findById($accountId);

        if ($account === null) {
            throw new AccountNotFoundException(sprintf('A conta "%s" não foi encontrada.', $accountId));
        }

        $account->debit($amount);

        $transaction = Transaction::record(
            TransactionId::generate(),
            $accountId,
            TransactionType::DEBIT,
            $amount,
            $description,
            $occurredAt,
        );

        $this->accounts->save($account);
        $this->transactions->save($transaction);

        return $transaction;
    }
}
