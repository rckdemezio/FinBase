<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Presentation\Web\Controller;

use Demezio\Finbase\Core\Http\HtmlResponse;
use Demezio\Finbase\Core\Http\InvalidRequestDataException;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\View\View;
use Demezio\Finbase\Finance\Application\UseCase\ListTransactions\ListTransactions;
use Demezio\Finbase\Finance\Domain\Entity\Transaction;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Presentation\Http\Presenter\TransactionPresenter;

final class ListTransactionsPageController
{
    public function __construct(private readonly ListTransactions $listTransactions, private readonly TransactionPresenter $presenter, private readonly View $view) {}

    public function __invoke(Request $request): HtmlResponse
    {
        $id = $request->routeParameter('id');
        if ($id === null) { throw new InvalidRequestDataException('O parâmetro "id" é obrigatório.'); }
        $transactions = array_map(fn (Transaction $transaction): array => $this->presenter->present($transaction), $this->listTransactions->execute(AccountId::fromString($id)));
        return new HtmlResponse($this->view->render('layouts/app', ['title' => 'Transações', 'content' => $this->view->render('transactions/index', ['accountId' => $id, 'transactions' => $transactions]) ]));
    }
}
