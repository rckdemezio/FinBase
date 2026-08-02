<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Presentation\Http\Controller;

use Demezio\Finbase\Core\Http\InvalidRequestDataException;
use Demezio\Finbase\Core\Http\JsonResponse;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Finance\Application\UseCase\CreditAccount\CreditAccount;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use Demezio\Finbase\Finance\Presentation\Http\Presenter\AccountPresenter;

/**
 * Traduz a operação de crédito entre HTTP e o caso de uso da aplicação.
 */
final class CreditAccountController
{
    public function __construct(
        private readonly CreditAccount $creditAccount,
        private readonly AccountPresenter $presenter,
    )
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $id = $request->routeParameter('id');
        $payload = $request->json();

        if ($id === null || ! is_int($payload['amount'] ?? null) || ! is_string($payload['currency'] ?? null)) {
            throw new InvalidRequestDataException('Os campos "id", "amount" e "currency" são obrigatórios.');
        }

        $account = $this->creditAccount->execute(
            AccountId::fromString($id),
            new Money($payload['amount'], $payload['currency']),
        );

        return new JsonResponse($this->presenter->present($account));
    }
}
