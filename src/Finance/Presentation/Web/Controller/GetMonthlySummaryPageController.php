<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Presentation\Web\Controller;

use Demezio\Finbase\Core\Http\HtmlResponse;
use Demezio\Finbase\Core\Http\InvalidRequestDataException;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\View\View;
use Demezio\Finbase\Finance\Application\Query\GetMonthlySummary\GetMonthlySummary;
use Demezio\Finbase\Finance\Application\UseCase\ListTransactions\ListTransactions;
use Demezio\Finbase\Finance\Domain\Entity\Transaction;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Presentation\Http\Presenter\MonthlySummaryPresenter;
use Demezio\Finbase\Finance\Presentation\Http\Presenter\TransactionPresenter;

final class GetMonthlySummaryPageController
{
    public function __construct(private readonly GetMonthlySummary $summary, private readonly ListTransactions $transactions, private readonly MonthlySummaryPresenter $summaryPresenter, private readonly TransactionPresenter $transactionPresenter, private readonly View $view) {}

    public function __invoke(Request $request): HtmlResponse
    {
        $id = $request->routeParameter('id'); $query = $request->query();
        if ($id === null || filter_var($query['year'] ?? null, FILTER_VALIDATE_INT) === false || filter_var($query['month'] ?? null, FILTER_VALIDATE_INT) === false) { throw new InvalidRequestDataException('Os parâmetros "id", "year" e "month" são obrigatórios.'); }
        $year = (int) $query['year']; $month = (int) $query['month']; $accountId = AccountId::fromString($id);
        $summary = $this->summaryPresenter->present($this->summary->execute($accountId, $year, $month));
        $transactions = array_values(array_map(fn (Transaction $transaction): array => $this->transactionPresenter->present($transaction), array_filter($this->transactions->execute($accountId), static fn (Transaction $transaction): bool => (int) $transaction->occurredAt()->format('Y') === $year && (int) $transaction->occurredAt()->format('n') === $month)));
        return new HtmlResponse($this->view->render('layouts/app', ['title' => 'Resumo mensal', 'content' => $this->view->render('accounts/summary', compact('id', 'summary', 'transactions'))]));
    }
}
