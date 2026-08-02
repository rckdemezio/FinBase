<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Presentation\Web\Controller;

use Demezio\Finbase\Core\Http\HtmlResponse;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\View\View;
use Demezio\Finbase\Finance\Application\UseCase\ListAccounts\ListAccounts;

/**
 * Renderiza a página HTML de listagem de contas.
 */
final class ListAccountsPageController
{
    public function __construct(
        private readonly ListAccounts $listAccounts,
        private readonly View $view,
    ) {
    }

    public function __invoke(Request $request): HtmlResponse
    {
        $content = $this->view->render('accounts/index', [
            'accounts' => $this->listAccounts->execute(),
        ]);

        return new HtmlResponse($this->view->render('layouts/app', [
            'title' => 'Contas',
            'content' => $content,
        ]));
    }
}
