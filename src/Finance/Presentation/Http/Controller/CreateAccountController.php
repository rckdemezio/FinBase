<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Presentation\Http\Controller;

use Demezio\Finbase\Core\Http\JsonResponse;
use Demezio\Finbase\Core\Http\InvalidRequestDataException;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Finance\Application\UseCase\CreateAccount\CreateAccount;
use Demezio\Finbase\Finance\Presentation\Http\Presenter\AccountPresenter;

/**
 * Traduz a criação de contas entre HTTP e o caso de uso da aplicação.
 */
final class CreateAccountController
{
    public function __construct(
        private readonly CreateAccount $createAccount,
        private readonly AccountPresenter $presenter,
    )
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->json();

        if (
            ! is_string($payload['name'] ?? null)
            || ! is_string($payload['currency'] ?? null)
        ) {
            throw new InvalidRequestDataException('Os campos "name" e "currency" são obrigatórios.');
        }

        $account = $this->createAccount->execute($payload['name'], $payload['currency']);

        return new JsonResponse($this->presenter->present($account), 201);
    }
}
