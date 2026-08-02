<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Presentation\Web\Controller;

use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\View\View;
use Demezio\Finbase\Finance\Application\UseCase\ListAccounts\ListAccounts;
use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryAccountRepository;
use Demezio\Finbase\Finance\Presentation\Web\Controller\ListAccountsPageController;
use PHPUnit\Framework\TestCase;

final class ListAccountsPageControllerTest extends TestCase
{
    public function testItRendersAccountsAsHtml(): void
    {
        $repository = new InMemoryAccountRepository();
        $repository->save(Account::open(
            AccountId::fromString('550e8400-e29b-41d4-a716-446655440000'),
            'Conta <Principal>',
            'BRL',
        ));
        $view = new View(dirname(__DIR__).'/resources/views');

        $response = (new ListAccountsPageController(new ListAccounts($repository), $view))(
            new Request('GET', '/accounts'),
        );

        self::assertSame(200, $response->status());
        self::assertSame('text/html; charset=UTF-8', $response->headers()['Content-Type']);
        self::assertStringContainsString('Conta &lt;Principal&gt;', $response->content());
        self::assertStringContainsString('0,00 BRL', $response->content());
        self::assertStringContainsString('/assets/css/app.css', $response->content());
    }
}
