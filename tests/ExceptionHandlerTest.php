<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Core\Http;

use Demezio\Finbase\Core\Http\ExceptionHandler;
use Demezio\Finbase\Core\Http\InvalidJsonException;
use Demezio\Finbase\Core\Http\InvalidRequestDataException;
use Demezio\Finbase\Finance\Application\Exception\AccountNotFoundException;
use Demezio\Finbase\Finance\Domain\Exception\InvalidAccountNameException;
use PHPUnit\Framework\TestCase;

final class ExceptionHandlerTest extends TestCase
{
    public function testItMapsKnownExceptionsToTheirHttpStatus(): void
    {
        $handler = new ExceptionHandler();

        self::assertSame(400, $handler->handle(new InvalidJsonException())->status());
        self::assertSame(422, $handler->handle(new InvalidRequestDataException('Dados obrigatórios ausentes.'))->status());
        self::assertSame(422, $handler->handle(new InvalidAccountNameException('Nome inválido.'))->status());
        self::assertSame(404, $handler->handle(new AccountNotFoundException('Conta não encontrada.'))->status());
        self::assertSame(500, $handler->handle(new \RuntimeException())->status());
    }
}
