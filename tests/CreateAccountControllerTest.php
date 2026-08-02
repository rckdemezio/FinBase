<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Presentation\Http\Controller;

use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\Http\ExceptionHandler;
use Demezio\Finbase\Finance\Application\UseCase\CreateAccount\CreateAccount;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryAccountRepository;
use Demezio\Finbase\Finance\Presentation\Http\Controller\CreateAccountController;
use Demezio\Finbase\Finance\Presentation\Http\Presenter\AccountPresenter;
use PHPUnit\Framework\TestCase;

final class CreateAccountControllerTest extends TestCase
{
    public function testItCreatesAnAccountAndReturnsItAsJson(): void
    {
        $controller = $this->controller();

        $response = $controller(new Request('POST', '/accounts', body: '{"name":"Conta Principal","currency":"BRL"}'));

        self::assertSame(201, $response->status());

        $data = json_decode($response->content(), true, 512, JSON_THROW_ON_ERROR);

        self::assertIsString($data['id']);
        self::assertSame('Conta Principal', $data['name']);
        self::assertSame(['amount' => 0, 'currency' => 'BRL'], $data['balance']);
    }

    public function testItReturnsBadRequestForInvalidJson(): void
    {
        $response = $this->responseFor(new Request('POST', '/accounts', body: '{'));

        self::assertSame(400, $response->status());
        self::assertSame(
            ['message' => 'O corpo da requisição contém JSON inválido.'],
            json_decode($response->content(), true, 512, JSON_THROW_ON_ERROR),
        );
    }

    public function testItReturnsUnprocessableEntityForInvalidDomainData(): void
    {
        $response = $this->responseFor(new Request('POST', '/accounts', body: '{"name":" ","currency":"BRL"}'));

        self::assertSame(422, $response->status());
        self::assertSame(
            ['message' => 'O nome da conta não pode ser vazio.'],
            json_decode($response->content(), true, 512, JSON_THROW_ON_ERROR),
        );
    }

    private function controller(): CreateAccountController
    {
        return new CreateAccountController(new CreateAccount(new InMemoryAccountRepository()), new AccountPresenter());
    }

    private function responseFor(Request $request): \Demezio\Finbase\Core\Http\Response
    {
        try {
            return ($this->controller())($request);
        } catch (\Throwable $exception) {
            return (new ExceptionHandler())->handle($exception);
        }
    }
}
