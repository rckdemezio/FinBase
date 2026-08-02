<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Presentation\Http\Controller;

use Demezio\Finbase\Core\Http\InvalidRequestDataException;
use Demezio\Finbase\Core\Http\JsonResponse;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Finance\Application\UseCase\ListTransactions\ListTransactions;
use Demezio\Finbase\Finance\Domain\Entity\Transaction;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Presentation\Http\Presenter\TransactionPresenter;

final class ListTransactionsController
{
    public function __construct(private readonly ListTransactions $listTransactions, private readonly TransactionPresenter $presenter) {}

    public function __invoke(Request $request): JsonResponse
    {
        $id = $request->routeParameter('id');
        if ($id === null) { throw new InvalidRequestDataException('O parâmetro "id" é obrigatório.'); }
        return new JsonResponse(array_map(fn (Transaction $transaction): array => $this->presenter->present($transaction), $this->listTransactions->execute(AccountId::fromString($id))));
    }
}
