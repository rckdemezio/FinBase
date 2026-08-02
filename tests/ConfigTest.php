<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests;

use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    public function testItDefinesTheAccountsStoragePath(): void
    {
        /** @var array{storage: array{accounts: string, transactions: string}, views: string} $config */
        $config = require __DIR__.'/../config/app.php';

        self::assertSame(
            dirname(__DIR__).'/storage/accounts.json',
            $config['storage']['accounts'],
        );
        self::assertSame(dirname(__DIR__).'/resources/views', $config['views']);
        self::assertSame(dirname(__DIR__).'/storage/transactions.json', $config['storage']['transactions']);
    }
}
