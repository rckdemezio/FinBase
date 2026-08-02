<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Presentation\Http\Controller;

use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Finance\Application\UseCase\ListAccounts\ListAccounts;
use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Infrastructure\Persistence\InMemory\InMemoryAccountRepository;
use Demezio\Finbase\Finance\Presentation\Http\Controller\ListAccountsController;
use Demezio\Finbase\Finance\Presentation\Http\Presenter\AccountPresenter;
use PHPUnit\Framework\TestCase;

final class ListAccountsControllerTest extends TestCase
{
    public function testItReturnsAllAccountsAsJson(): void
    {
        $repository = new InMemoryAccountRepository();
        $repository->save(Account::open(
            AccountId::fromString('550e8400-e29b-41d4-a716-446655440000'),
            'Conta Principal',
            'BRL',
        ));

        $response = (new ListAccountsController(new ListAccounts($repository), new AccountPresenter()))(
            new Request('GET', '/accounts'),
        );

        self::assertSame(200, $response->status());
        self::assertSame(
            [[
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'name' => 'Conta Principal',
                'balance' => ['amount' => 0, 'currency' => 'BRL'],
            ]],
            json_decode($response->content(), true, 512, JSON_THROW_ON_ERROR),
        );
    }
}
