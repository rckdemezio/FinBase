<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Finance\Presentation\Web\Controller;

use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\View\View;
use Demezio\Finbase\Finance\Presentation\Web\Controller\CreateAccountPageController;
use PHPUnit\Framework\TestCase;

final class CreateAccountPageControllerTest extends TestCase
{
    public function testItRendersTheCreateAccountForm(): void
    {
        $response = (new CreateAccountPageController($this->view()))(new Request('GET', '/accounts/create'));

        self::assertSame(200, $response->status());
        self::assertStringContainsString('<form method="post" action="/accounts">', $response->content());
        self::assertStringContainsString('value="BRL"', $response->content());
    }

    private function view(): View
    {
        return new View(dirname(__DIR__).'/resources/views');
    }
}
