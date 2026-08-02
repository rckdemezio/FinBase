<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Presentation\Web\Controller;

use Demezio\Finbase\Core\Http\HtmlResponse;
use Demezio\Finbase\Core\Http\InvalidRequestDataException;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\View\View;
use Demezio\Finbase\Finance\Application\UseCase\GetAccount\GetAccount;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Presentation\Http\Presenter\AccountPresenter;

/**
 * Renderiza a página HTML de uma conta individual.
 */
final class GetAccountPageController
{
    public function __construct(
        private readonly GetAccount $getAccount,
        private readonly AccountPresenter $presenter,
        private readonly View $view,
    ) {
    }

    public function __invoke(Request $request): HtmlResponse
    {
        $id = $request->routeParameter('id');

        if ($id === null) {
            throw new InvalidRequestDataException('O parâmetro "id" é obrigatório.');
        }

        $account = $this->presenter->present($this->getAccount->execute(AccountId::fromString($id)));

        return new HtmlResponse($this->view->render('layouts/app', [
            'title' => $account['name'],
            'content' => $this->view->render('accounts/show', ['account' => $account]),
        ]));
    }
}
