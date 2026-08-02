<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Presentation\Web\Controller;

use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\View\View;
use Demezio\Finbase\Finance\Application\Exception\AccountNotFoundException;
use Demezio\Finbase\Finance\Application\UseCase\GetAccount\GetAccount;
use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryAccountRepository;
use Demezio\Finbase\Finance\Presentation\Web\Controller\CreateExpensePageController;
use Demezio\Finbase\Finance\Presentation\Web\Controller\CreateIncomePageController;
use PHPUnit\Framework\TestCase;

final class CreateTransactionPageControllerTest extends TestCase
{
    public function testItRendersTheIncomeForm(): void
    {
        [$accounts, $account] = $this->accountFixture();
        $controller = new CreateIncomePageController(new GetAccount($accounts), $this->view());

        $response = $controller($this->request('GET', '/accounts/'.$account->id().'/income/create', $account->id()));

        self::assertSame(200, $response->status());
        self::assertStringContainsString('Nova renda', $response->content());
        self::assertStringContainsString('/accounts/'.$account->id().'/income', $response->content());
        self::assertStringContainsString('data-money-input', $response->content());
        self::assertStringContainsString('name="amount"', $response->content());
    }

    public function testItRendersTheExpenseForm(): void
    {
        [$accounts, $account] = $this->accountFixture();
        $controller = new CreateExpensePageController(new GetAccount($accounts), $this->view());

        $response = $controller($this->request('GET', '/accounts/'.$account->id().'/expenses/create', $account->id()));

        self::assertSame(200, $response->status());
        self::assertStringContainsString('Nova despesa', $response->content());
        self::assertStringContainsString('/accounts/'.$account->id().'/expenses', $response->content());
        self::assertStringContainsString('data-money-input', $response->content());
        self::assertStringContainsString('name="amount"', $response->content());
    }

    public function testAFormForAnUnknownAccountKeepsTheCurrentNotFoundTreatment(): void
    {
        $controller = new CreateIncomePageController(new GetAccount(new InMemoryAccountRepository()), $this->view());

        $this->expectException(AccountNotFoundException::class);

        $controller($this->request('GET', '/accounts/'.$this->id().'/income/create', $this->id()));
    }

    /** @return array{InMemoryAccountRepository, Account} */
    private function accountFixture(): array
    {
        $accounts = new InMemoryAccountRepository();
        $account = Account::open($this->id(), 'Conta Principal', 'BRL');
        $accounts->save($account);

        return [$accounts, $account];
    }

    private function request(string $method, string $uri, AccountId $id): Request
    {
        return (new Request($method, $uri))->withRouteParameters(['id' => $id->value()]);
    }

    private function id(): AccountId
    {
        return AccountId::fromString('550e8400-e29b-41d4-a716-446655440000');
    }

    private function view(): View
    {
        return new View(dirname(__DIR__).'/resources/views');
    }
}
