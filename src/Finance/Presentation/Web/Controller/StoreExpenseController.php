<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Presentation\Web\Controller;

use Demezio\Finbase\Core\Http\HtmlResponse;
use Demezio\Finbase\Core\Http\InvalidRequestDataException;
use Demezio\Finbase\Core\Http\RedirectResponse;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\Http\Response;
use Demezio\Finbase\Core\View\View;
use Demezio\Finbase\Finance\Application\UseCase\GetAccount\GetAccount;
use Demezio\Finbase\Finance\Application\UseCase\RecordExpense\RecordExpense;
use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\Exception\InsufficientFundsException;
use Demezio\Finbase\Finance\Domain\Exception\InvalidAccountAmountException;
use Demezio\Finbase\Finance\Domain\Exception\InvalidTransactionAmountException;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;

final class StoreExpenseController
{
    public function __construct(
        private readonly GetAccount $getAccount,
        private readonly RecordExpense $recordExpense,
        private readonly View $view,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $id = $request->routeParameter('id');

        if ($id === null) {
            throw new InvalidRequestDataException('O parâmetro "id" é obrigatório.');
        }

        $accountId = AccountId::fromString($id);
        $account = $this->getAccount->execute($accountId);
        $form = $request->form();
        $amountValue = $form['amount'] ?? null;
        $description = $form['description'] ?? null;
        $occurredAtValue = $form['occurredAt'] ?? null;

        if (! is_string($amountValue) || ! is_string($description) || ! is_string($occurredAtValue)) {
            return $this->renderForm($account, ['Os campos "amount", "description" e "occurredAt" são obrigatórios.'], $form);
        }

        $amount = filter_var($amountValue, FILTER_VALIDATE_INT);

        if ($amount === false) {
            return $this->renderForm($account, ['O valor deve ser um inteiro em centavos.'], $form);
        }

        $occurredAt = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $occurredAtValue);
        $dateErrors = \DateTimeImmutable::getLastErrors();

        if ($occurredAt === false || ($dateErrors !== false && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0))) {
            return $this->renderForm($account, ['A data informada é inválida.'], $form);
        }

        try {
            $this->recordExpense->execute(
                $accountId,
                new Money($amount, $account->balance()->currencyCode()),
                $description,
                $occurredAt,
            );
        } catch (InvalidAccountAmountException | InvalidTransactionAmountException | InsufficientFundsException $exception) {
            return $this->renderForm($account, [$exception->getMessage()], $form);
        }

        return new RedirectResponse('/accounts/'.$id);
    }

    /**
     * @param list<string> $errors
     * @param array<string, mixed> $old
     */
    private function renderForm(Account $account, array $errors, array $old): HtmlResponse
    {
        return new HtmlResponse($this->view->render('layouts/app', [
            'title' => 'Nova despesa',
            'content' => $this->view->render('transactions/expense-create', compact('account', 'errors', 'old')),
        ]), 422);
    }
}
