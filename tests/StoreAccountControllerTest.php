<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Presentation\Web\Controller;

use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\Http\Response;
use Demezio\Finbase\Core\View\View;
use Demezio\Finbase\Finance\Application\UseCase\CreateAccount\CreateAccount;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryAccountRepository;
use Demezio\Finbase\Finance\Presentation\Web\Controller\StoreAccountController;
use PHPUnit\Framework\TestCase;

final class StoreAccountControllerTest extends TestCase
{
    public function testItRedirectsAfterCreatingAnAccount(): void
    {
        $response = ($this->controller())(new Request('POST', '/accounts', form: [
            'name' => 'Conta Principal',
            'currency' => 'BRL',
        ]));

        self::assertSame(302, $response->status());
        self::assertSame('/accounts', $response->headers()['Location']);
    }

    public function testItRendersTheFormWithValidationErrors(): void
    {
        $response = ($this->controller())(new Request('POST', '/accounts', form: [
            'name' => ' ',
            'currency' => 'BRL',
        ]));

        self::assertSame(422, $response->status());
        self::assertStringContainsString('O nome da conta não pode ser vazio.', $response->content());
    }

    private function controller(): StoreAccountController
    {
        return new StoreAccountController(
            new CreateAccount(new InMemoryAccountRepository()),
            new View(dirname(__DIR__).'/resources/views'),
        );
    }
}
