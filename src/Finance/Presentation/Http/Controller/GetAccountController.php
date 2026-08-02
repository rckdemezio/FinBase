<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Presentation\Http\Controller;

use Demezio\Finbase\Core\Http\InvalidRequestDataException;
use Demezio\Finbase\Core\Http\JsonResponse;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Finance\Application\UseCase\GetAccount\GetAccount;
use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;

/**
 * Traduz a consulta de uma conta entre HTTP e o caso de uso da aplicação.
 */
final class GetAccountController
{
    public function __construct(private readonly GetAccount $getAccount)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $id = $request->routeParameter('id');

        if ($id === null) {
            throw new InvalidRequestDataException('O parâmetro "id" é obrigatório.');
        }

        return new JsonResponse($this->accountData($this->getAccount->execute(AccountId::fromString($id))));
    }

    /**
     * @return array{id: string, name: string, balance: array{amount: int, currency: string}}
     */
    private function accountData(Account $account): array
    {
        return [
            'id' => $account->id()->value(),
            'name' => $account->name(),
            'balance' => [
                'amount' => $account->balance()->amount(),
                'currency' => $account->balance()->currency(),
            ],
        ];
    }
}
