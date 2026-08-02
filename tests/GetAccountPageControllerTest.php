<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Presentation\Web\Controller;

use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\View\View;
use Demezio\Finbase\Finance\Application\UseCase\GetAccount\GetAccount;
use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryAccountRepository;
use Demezio\Finbase\Finance\Presentation\Http\Presenter\AccountPresenter;
use Demezio\Finbase\Finance\Presentation\Web\Controller\GetAccountPageController;
use PHPUnit\Framework\TestCase;

final class GetAccountPageControllerTest extends TestCase
{
    public function testItRendersTheRequestedAccountAsHtml(): void
    {
        $repository = new InMemoryAccountRepository();
        $id = AccountId::fromString('550e8400-e29b-41d4-a716-446655440000');
        $repository->save(Account::restore($id, 'Conta <Principal>', new Money(10000, 'BRL')));
        $controller = new GetAccountPageController(
            new GetAccount($repository),
            new AccountPresenter(),
            new View(dirname(__DIR__).'/resources/views'),
        );

        $response = $controller(
            (new Request('GET', '/accounts/'.$id->value()))->withRouteParameters(['id' => $id->value()]),
        );

        self::assertSame(200, $response->status());
        self::assertStringContainsString('Conta &lt;Principal&gt;', $response->content());
        self::assertStringContainsString($id->value(), $response->content());
        self::assertStringContainsString('10000', $response->content());
        self::assertStringContainsString('← Voltar para contas', $response->content());
    }
}
