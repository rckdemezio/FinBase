<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Core\View;

use Demezio\Finbase\Core\View\View;
use PHPUnit\Framework\TestCase;

final class ViewTest extends TestCase
{
    public function testItRendersATemplateWithTheProvidedData(): void
    {
        $view = new View(dirname(__DIR__).'/resources/views');

        $content = $view->render('layouts/app', [
            'title' => 'Teste',
            'content' => '<p>Conteúdo</p>',
        ]);

        self::assertStringContainsString('<title>Teste · FinBase</title>', $content);
        self::assertStringContainsString('<p>Conteúdo</p>', $content);
    }
}
