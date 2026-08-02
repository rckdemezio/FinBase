<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Presentation\Web\Controller;

use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\View\View;
use Demezio\Finbase\Finance\Application\Exception\AccountNotFoundException;
use Demezio\Finbase\Finance\Application\UseCase\GetAccount\GetAccount;
use Demezio\Finbase\Finance\Application\UseCase\RecordExpense\RecordExpense;
use Demezio\Finbase\Finance\Application\UseCase\RecordIncome\RecordIncome;
use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\Enum\TransactionType;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryAccountRepository;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryTransactionRepository;
use Demezio\Finbase\Finance\Presentation\Web\Controller\StoreExpenseController;
use Demezio\Finbase\Finance\Presentation\Web\Controller\StoreIncomeController;
use PHPUnit\Framework\TestCase;

final class StoreTransactionControllerTest extends TestCase
{
    public function testValidIncomeRecordsACreditAndRedirects(): void
    {
        [$controller, $transactions, $account] = $this->incomeController();

        $response = $controller($this->request($account->id(), [
            'amount' => '15000',
            'description' => 'Salário',
            'occurredAt' => '2026-08-02T14:30',
        ], 'income'));

        $transaction = $transactions->all()[0];
        self::assertSame(302, $response->status());
        self::assertSame('/accounts/'.$account->id(), $response->headers()['Location']);
        self::assertSame(TransactionType::CREDIT, $transaction->type());
        self::assertSame(15000, $transaction->amount()->amount());
        self::assertSame('2026-08-02T14:30', $transaction->occurredAt()->format('Y-m-d\TH:i'));
    }

    public function testValidExpenseRecordsADebitAndRedirects(): void
    {
        [$controller, $transactions, $account] = $this->expenseController(20000);

        $response = $controller($this->request($account->id(), [
            'amount' => '5000',
            'description' => 'Mercado',
            'occurredAt' => '2026-08-02T14:30',
        ], 'expenses'));

        $transaction = $transactions->all()[0];
        self::assertSame(302, $response->status());
        self::assertSame('/accounts/'.$account->id(), $response->headers()['Location']);
        self::assertSame(TransactionType::DEBIT, $transaction->type());
        self::assertSame(5000, $transaction->amount()->amount());
    }

    public function testNonIntegerZeroAndNegativeAmountsRenderTheFormWith422(): void
    {
        foreach (['100abc', '0', '-1'] as $amount) {
            [$controller, $transactions, $account] = $this->incomeController();

            $response = $controller($this->request($account->id(), [
                'amount' => $amount,
                'description' => 'Salário',
                'occurredAt' => '2026-08-02T14:30',
            ], 'income'));

            self::assertSame(422, $response->status(), 'Falhou para amount='.$amount);
            self::assertSame([], $transactions->all());
            self::assertStringContainsString(htmlspecialchars($amount, ENT_QUOTES, 'UTF-8'), $response->content());
        }
    }

    public function testInvalidDateRendersTheFormWith422(): void
    {
        [$controller, $transactions, $account] = $this->incomeController();

        $response = $controller($this->request($account->id(), [
            'amount' => '100',
            'description' => 'Salário',
            'occurredAt' => '2026-02-30T14:30',
        ], 'income'));

        self::assertSame(422, $response->status());
        self::assertSame([], $transactions->all());
        self::assertStringContainsString('A data informada é inválida.', $response->content());
    }

    public function testInsufficientFundsRenderTheExpenseFormWith422(): void
    {
        [$controller, $transactions, $account] = $this->expenseController(100);

        $response = $controller($this->request($account->id(), [
            'amount' => '101',
            'description' => 'Mercado',
            'occurredAt' => '2026-08-02T14:30',
        ], 'expenses'));

        self::assertSame(422, $response->status());
        self::assertSame([], $transactions->all());
        self::assertStringContainsString('saldo suficiente', $response->content());
    }

    public function testAnUnknownAccountKeepsTheCurrentNotFoundTreatment(): void
    {
        $accounts = new InMemoryAccountRepository();
        $controller = new StoreIncomeController(
            new GetAccount($accounts),
            new RecordIncome($accounts, new InMemoryTransactionRepository()),
            $this->view(),
        );

        $this->expectException(AccountNotFoundException::class);

        $controller($this->request($this->id(), [
            'amount' => '100',
            'description' => 'Salário',
            'occurredAt' => '2026-08-02T14:30',
        ], 'income'));
    }

    public function testCurrencySentByTheFormIsIgnored(): void
    {
        [$controller, $transactions, $account] = $this->incomeController();

        $response = $controller($this->request($account->id(), [
            'amount' => '100',
            'description' => 'Salário',
            'occurredAt' => '2026-08-02T14:30',
            'currency' => 'USD',
        ], 'income'));

        self::assertSame(302, $response->status());
        self::assertSame('BRL', $transactions->all()[0]->amount()->currencyCode());
    }

    /** @return array{StoreIncomeController, InMemoryTransactionRepository, Account} */
    private function incomeController(): array
    {
        $accounts = new InMemoryAccountRepository();
        $transactions = new InMemoryTransactionRepository();
        $account = Account::open($this->id(), 'Conta Principal', 'BRL');
        $accounts->save($account);

        return [
            new StoreIncomeController(new GetAccount($accounts), new RecordIncome($accounts, $transactions), $this->view()),
            $transactions,
            $account,
        ];
    }

    /** @return array{StoreExpenseController, InMemoryTransactionRepository, Account} */
    private function expenseController(int $balance): array
    {
        $accounts = new InMemoryAccountRepository();
        $transactions = new InMemoryTransactionRepository();
        $account = Account::restore($this->id(), 'Conta Principal', new Money($balance, 'BRL'));
        $accounts->save($account);

        return [
            new StoreExpenseController(new GetAccount($accounts), new RecordExpense($accounts, $transactions), $this->view()),
            $transactions,
            $account,
        ];
    }

    /** @param array<string, mixed> $form */
    private function request(AccountId $id, array $form, string $operation): Request
    {
        return (new Request('POST', '/accounts/'.$id.'/'.$operation, form: $form))
            ->withRouteParameters(['id' => $id->value()]);
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
