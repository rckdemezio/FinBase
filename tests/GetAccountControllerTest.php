<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Presentation\Http\Controller;

use Demezio\Finbase\Core\Http\ExceptionHandler;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\Http\Response;
use Demezio\Finbase\Finance\Application\UseCase\GetAccount\GetAccount;
use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryAccountRepository;
use Demezio\Finbase\Finance\Presentation\Http\Controller\GetAccountController;
use PHPUnit\Framework\TestCase;

final class GetAccountControllerTest extends TestCase
{
    public function testItReturnsTheRequestedAccount(): void
    {
        $repository = new InMemoryAccountRepository();
        $id = AccountId::fromString('550e8400-e29b-41d4-a716-446655440000');
        $repository->save(Account::restore($id, 'Conta Principal', new \Demezio\Finbase\Finance\Domain\ValueObject\Money(10000, 'BRL')));

        $response = (new GetAccountController(new GetAccount($repository)))(
            (new Request('GET', '/accounts/'.$id->value()))->withRouteParameters(['id' => $id->value()]),
        );

        self::assertSame(200, $response->status());
        self::assertSame(
            [
                'id' => $id->value(),
                'name' => 'Conta Principal',
                'balance' => ['amount' => 10000, 'currency' => 'BRL'],
            ],
            json_decode($response->content(), true, 512, JSON_THROW_ON_ERROR),
        );
    }

    public function testItMapsAnUnknownAccountToNotFound(): void
    {
        $controller = new GetAccountController(new GetAccount(new InMemoryAccountRepository()));
        $request = (new Request('GET', '/accounts/550e8400-e29b-41d4-a716-446655440000'))
            ->withRouteParameters(['id' => '550e8400-e29b-41d4-a716-446655440000']);

        $response = $this->handle($controller, $request);

        self::assertSame(404, $response->status());
    }

    private function handle(GetAccountController $controller, Request $request): Response
    {
        try {
            return $controller($request);
        } catch (\Throwable $exception) {
            return (new ExceptionHandler())->handle($exception);
        }
    }
}
