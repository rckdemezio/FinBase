<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Presentation\Web\Controller;

use Demezio\Finbase\Core\Http\HtmlResponse;
use Demezio\Finbase\Core\Http\InvalidRequestDataException;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\View\View;
use Demezio\Finbase\Finance\Application\UseCase\GetAccount\GetAccount;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;

final class CreateExpensePageController
{
    public function __construct(
        private readonly GetAccount $getAccount,
        private readonly View $view,
    ) {
    }

    public function __invoke(Request $request): HtmlResponse
    {
        $id = $request->routeParameter('id');

        if ($id === null) {
            throw new InvalidRequestDataException('O parâmetro "id" é obrigatório.');
        }

        $account = $this->getAccount->execute(AccountId::fromString($id));

        return new HtmlResponse($this->view->render('layouts/app', [
            'title' => 'Nova despesa',
            'content' => $this->view->render('transactions/expense-create', [
                'account' => $account,
                'errors' => [],
                'old' => ['amount' => '', 'description' => '', 'occurredAt' => ''],
            ]),
        ]));
    }
}
