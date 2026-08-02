<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Presentation\Http\Controller;

use Demezio\Finbase\Core\Http\JsonResponse;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Finance\Application\UseCase\ListAccounts\ListAccounts;
use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Presentation\Http\Presenter\AccountPresenter;

/**
 * Traduz a listagem de contas entre HTTP e o caso de uso da aplicação.
 */
final class ListAccountsController
{
    public function __construct(
        private readonly ListAccounts $listAccounts,
        private readonly AccountPresenter $presenter,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        return new JsonResponse(array_map(
            fn (Account $account): array => $this->presenter->present($account),
            $this->listAccounts->execute(),
        ));
    }
}
