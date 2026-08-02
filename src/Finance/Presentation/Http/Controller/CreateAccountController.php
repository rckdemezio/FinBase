<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Presentation\Http\Controller;

use Demezio\Finbase\Core\Http\JsonResponse;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Finance\Application\UseCase\CreateAccount\CreateAccount;
use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\Exception\InvalidAccountNameException;
use Demezio\Finbase\Finance\Domain\Exception\InvalidCurrencyException;

/**
 * Traduz a criação de contas entre HTTP e o caso de uso da aplicação.
 */
final class CreateAccountController
{
    public function __construct(private readonly CreateAccount $createAccount)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $payload = json_decode($request->body(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return new JsonResponse(['message' => 'O corpo da requisição contém JSON inválido.'], 400);
        }

        if (
            ! is_array($payload)
            || ! is_string($payload['name'] ?? null)
            || ! is_string($payload['currency'] ?? null)
        ) {
            return new JsonResponse(['message' => 'Os campos "name" e "currency" são obrigatórios.'], 422);
        }

        try {
            $account = $this->createAccount->execute($payload['name'], $payload['currency']);
        } catch (InvalidAccountNameException | InvalidCurrencyException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 422);
        } catch (\Throwable) {
            return new JsonResponse(['message' => 'Ocorreu um erro ao criar a conta.'], 500);
        }

        return new JsonResponse($this->accountData($account), 201);
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
