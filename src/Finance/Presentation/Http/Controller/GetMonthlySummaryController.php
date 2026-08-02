<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Presentation\Http\Controller;

use Demezio\Finbase\Core\Http\InvalidRequestDataException;
use Demezio\Finbase\Core\Http\JsonResponse;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Finance\Application\Query\GetMonthlySummary\GetMonthlySummary;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Presentation\Http\Presenter\MonthlySummaryPresenter;

final class GetMonthlySummaryController
{
    public function __construct(private readonly GetMonthlySummary $getMonthlySummary, private readonly MonthlySummaryPresenter $presenter) {}

    public function __invoke(Request $request): JsonResponse
    {
        $id = $request->routeParameter('id'); $query = $request->query();
        if ($id === null || filter_var($query['year'] ?? null, FILTER_VALIDATE_INT) === false || filter_var($query['month'] ?? null, FILTER_VALIDATE_INT) === false) {
            throw new InvalidRequestDataException('Os parâmetros "id", "year" e "month" são obrigatórios.');
        }
        return new JsonResponse($this->presenter->present($this->getMonthlySummary->execute(AccountId::fromString($id), (int) $query['year'], (int) $query['month'])));
    }
}
