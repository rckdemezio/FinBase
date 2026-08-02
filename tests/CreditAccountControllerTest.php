<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Presentation\Http\Controller;

use Demezio\Finbase\Core\Http\ExceptionHandler;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\Http\Response;
use Demezio\Finbase\Finance\Application\UseCase\CreditAccount\CreditAccount;
use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryAccountRepository;
use Demezio\Finbase\Finance\Presentation\Http\Controller\CreditAccountController;
use Demezio\Finbase\Finance\Presentation\Http\Presenter\AccountPresenter;
use PHPUnit\Framework\TestCase;

final class CreditAccountControllerTest extends TestCase
{
    public function testItCreditsTheAccountAndReturnsItsUpdatedState(): void
    {
        $repository = new InMemoryAccountRepository();
        $id = $this->id();
        $repository->save(Account::open($id, 'Conta Principal', 'BRL'));

        $response = (new CreditAccountController(new CreditAccount($repository), new AccountPresenter()))(
            (new Request('POST', '/accounts/'.$id.'/credits', body: '{"amount":15000,"currency":"BRL"}'))
                ->withRouteParameters(['id' => $id->value()]),
        );

        self::assertSame(200, $response->status());
        self::assertSame(
            ['amount' => 15000, 'currency' => 'BRL'],
            json_decode($response->content(), true, 512, JSON_THROW_ON_ERROR)['balance'],
        );
    }

    public function testItMapsACurrencyMismatchToUnprocessableEntity(): void
    {
        $repository = new InMemoryAccountRepository();
        $id = $this->id();
        $repository->save(Account::open($id, 'Conta Principal', 'BRL'));
        $controller = new CreditAccountController(new CreditAccount($repository), new AccountPresenter());
        $request = (new Request('POST', '/accounts/'.$id.'/credits', body: '{"amount":15000,"currency":"USD"}'))
            ->withRouteParameters(['id' => $id->value()]);

        self::assertSame(422, $this->handle($controller, $request)->status());
    }

    private function id(): AccountId
    {
        return AccountId::fromString('550e8400-e29b-41d4-a716-446655440000');
    }

    private function handle(CreditAccountController $controller, Request $request): Response
    {
        try {
            return $controller($request);
        } catch (\Throwable $exception) {
            return (new ExceptionHandler())->handle($exception);
        }
    }
}
