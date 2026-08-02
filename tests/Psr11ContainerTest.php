<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Core\Container;

use Demezio\Finbase\Core\Container\Container;
use Demezio\Finbase\Core\Exceptions\NotFoundBindingException;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use PHPUnit\Framework\TestCase;

final class Psr11ContainerTest extends TestCase
{
    public function testImplementsPsrContainerInterface(): void
    {
        self::assertInstanceOf(PsrContainerInterface::class, new Container());
    }

    public function testGetResolvesConcreteEntry(): void
    {
        $entry = (new Container())->get(PsrResolvableService::class);

        self::assertInstanceOf(PsrResolvableService::class, $entry);
    }

    public function testHasReturnsFalseForUnresolvableEntry(): void
    {
        $container = new Container();

        self::assertFalse($container->has(PsrUnresolvableContract::class));
        self::assertFalse($container->has('Demezio\\Finbase\\Tests\\MissingEntry'));
    }

    public function testGetThrowsPsrNotFoundExceptionForUnknownEntry(): void
    {
        $container = new Container();

        try {
            $container->get('Demezio\\Finbase\\Tests\\MissingEntry');
            self::fail('Era esperada uma exceção de entrada não encontrada.');
        } catch (NotFoundBindingException $exception) {
            self::assertInstanceOf(NotFoundExceptionInterface::class, $exception);
        }
    }
}

final class PsrResolvableService
{
}

interface PsrUnresolvableContract
{
}
