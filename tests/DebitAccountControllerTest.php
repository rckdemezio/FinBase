<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Presentation\Http\Controller;

use Demezio\Finbase\Core\Http\ExceptionHandler;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\Http\Response;
use Demezio\Finbase\Finance\Application\UseCase\DebitAccount\DebitAccount;
use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryAccountRepository;
use Demezio\Finbase\Finance\Presentation\Http\Controller\DebitAccountController;
use Demezio\Finbase\Finance\Presentation\Http\Presenter\AccountPresenter;
use PHPUnit\Framework\TestCase;

final class DebitAccountControllerTest extends TestCase
{
    public function testItDebitsTheAccountAndReturnsItsUpdatedState(): void
    {
        $repository = new InMemoryAccountRepository();
        $id = $this->id();
        $repository->save(Account::restore($id, 'Conta Principal', new Money(20000, 'BRL')));

        $response = (new DebitAccountController(new DebitAccount($repository), new AccountPresenter()))(
            (new Request('POST', '/accounts/'.$id.'/debits', body: '{"amount":15000,"currency":"BRL"}'))
                ->withRouteParameters(['id' => $id->value()]),
        );

        self::assertSame(200, $response->status());
        self::assertSame(
            ['amount' => 5000, 'currency' => 'BRL'],
            json_decode($response->content(), true, 512, JSON_THROW_ON_ERROR)['balance'],
        );
    }

    public function testItMapsInsufficientFundsToUnprocessableEntity(): void
    {
        $repository = new InMemoryAccountRepository();
        $id = $this->id();
        $repository->save(Account::open($id, 'Conta Principal', 'BRL'));
        $controller = new DebitAccountController(new DebitAccount($repository), new AccountPresenter());
        $request = (new Request('POST', '/accounts/'.$id.'/debits', body: '{"amount":1,"currency":"BRL"}'))
            ->withRouteParameters(['id' => $id->value()]);

        self::assertSame(422, $this->handle($controller, $request)->status());
    }

    private function id(): AccountId
    {
        return AccountId::fromString('550e8400-e29b-41d4-a716-446655440000');
    }

    private function handle(DebitAccountController $controller, Request $request): Response
    {
        try {
            return $controller($request);
        } catch (\Throwable $exception) {
            return (new ExceptionHandler())->handle($exception);
        }
    }
}
