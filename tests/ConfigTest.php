<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests;

use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    public function testItDefinesTheAccountsStoragePath(): void
    {
        /** @var array{storage: array{accounts: string}} $config */
        $config = require __DIR__.'/../config/app.php';

        self::assertSame(
            dirname(__DIR__).'/storage/accounts.json',
            $config['storage']['accounts'],
        );
    }
}
