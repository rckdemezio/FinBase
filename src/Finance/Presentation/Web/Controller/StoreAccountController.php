<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Presentation\Web\Controller;

use Demezio\Finbase\Core\Http\HtmlResponse;
use Demezio\Finbase\Core\Http\RedirectResponse;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\Http\Response;
use Demezio\Finbase\Core\View\View;
use Demezio\Finbase\Finance\Application\UseCase\CreateAccount\CreateAccount;
use Demezio\Finbase\Finance\Domain\Exception\InvalidAccountNameException;
use Demezio\Finbase\Finance\Domain\Exception\InvalidCurrencyException;

/**
 * Traduz o envio do formulário HTML na criação de uma conta.
 */
final class StoreAccountController
{
    public function __construct(
        private readonly CreateAccount $createAccount,
        private readonly View $view,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $form = $request->form();
        $name = $form['name'] ?? null;
        $currency = $form['currency'] ?? null;

        if (! is_string($name) || ! is_string($currency)) {
            return $this->renderForm(
                ['Os campos "name" e "currency" são obrigatórios.'],
                $form,
            );
        }

        try {
            $this->createAccount->execute($name, $currency);
        } catch (InvalidAccountNameException | InvalidCurrencyException $exception) {
            return $this->renderForm([$exception->getMessage()], $form);
        }

        return new RedirectResponse('/accounts');
    }

    /**
     * @param list<string> $errors
     * @param array<string, mixed> $old
     */
    private function renderForm(array $errors, array $old): HtmlResponse
    {
        return new HtmlResponse($this->view->render('layouts/app', [
            'title' => 'Nova conta',
            'content' => $this->view->render('accounts/create', [
                'errors' => $errors,
                'old' => $old,
            ]),
        ]), 422);
    }
}
