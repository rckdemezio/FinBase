<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Presentation\Web\Controller;

use Demezio\Finbase\Core\Http\HtmlResponse;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\View\View;

/**
 * Renderiza o formulário HTML de criação de contas.
 */
final class CreateAccountPageController
{
    public function __construct(private readonly View $view)
    {
    }

    public function __invoke(Request $request): HtmlResponse
    {
        return new HtmlResponse($this->view->render('layouts/app', [
            'title' => 'Nova conta',
            'content' => $this->view->render('accounts/create', [
                'errors' => [],
                'old' => ['name' => '', 'currency' => 'BRL'],
            ]),
        ]));
    }
}
