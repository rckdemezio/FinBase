<?php

declare(strict_types=1);

namespace Demezio\Finbase\Core\Http;

use Demezio\Finbase\Finance\Application\Exception\AccountNotFoundException;
use Demezio\Finbase\Finance\Domain\Exception\InvalidAccountNameException;
use Demezio\Finbase\Finance\Domain\Exception\InvalidAccountAmountException;
use Demezio\Finbase\Finance\Domain\Exception\InvalidAccountIdException;
use Demezio\Finbase\Finance\Domain\Exception\InvalidCurrencyException;
use Demezio\Finbase\Finance\Domain\Exception\CurrencyMismatchException;
use Demezio\Finbase\Finance\Domain\Exception\InsufficientFundsException;

/**
 * Converte exceções da aplicação em respostas HTTP padronizadas.
 */
final class ExceptionHandler
{
    public function handle(\Throwable $exception): Response
    {
        return match (true) {
            $exception instanceof InvalidJsonException => new JsonResponse([
                'message' => 'O corpo da requisição contém JSON inválido.',
            ], 400),
            $exception instanceof InvalidRequestDataException,
            $exception instanceof InvalidAccountIdException,
            $exception instanceof InvalidAccountNameException,
            $exception instanceof InvalidAccountAmountException,
            $exception instanceof InvalidCurrencyException => new JsonResponse([
                'message' => $exception->getMessage(),
            ], 422),
            $exception instanceof CurrencyMismatchException,
            $exception instanceof InsufficientFundsException => new JsonResponse([
                'message' => $exception->getMessage(),
            ], 422),
            $exception instanceof AccountNotFoundException => new JsonResponse([
                'message' => $exception->getMessage(),
            ], 404),
            default => new JsonResponse(['message' => 'Ocorreu um erro interno.'], 500),
        };
    }
}
