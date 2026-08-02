<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Presentation\Http\Presenter;

use Demezio\Finbase\Finance\Domain\Entity\Account;
use Demezio\Finbase\Finance\Domain\ValueObject\AccountId;
use Demezio\Finbase\Finance\Domain\ValueObject\Money;
use Demezio\Finbase\Finance\Presentation\Http\Presenter\AccountPresenter;
use PHPUnit\Framework\TestCase;

final class AccountPresenterTest extends TestCase
{
    public function testItPresentsAnAccountForTheHttpApi(): void
    {
        $account = Account::restore(
            AccountId::fromString('550e8400-e29b-41d4-a716-446655440000'),
            'Conta Principal',
            new Money(10000, 'BRL'),
        );

        self::assertSame(
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'name' => 'Conta Principal',
                'balance' => ['amount' => 10000, 'currency' => 'BRL'],
            ],
            (new AccountPresenter())->present($account),
        );
    }
}
