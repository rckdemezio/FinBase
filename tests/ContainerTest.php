<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Core\Container;

use Demezio\Finbase\Core\Container\Binding;
use Demezio\Finbase\Core\Container\Container;
use Demezio\Finbase\Core\Exceptions\ContainerException;
use PHPUnit\Framework\TestCase;

final class ContainerTest extends TestCase
{
    public function testBindingStoresConcreteClassAndSharingRule(): void
    {
        $binding = new Binding(\DateTimeImmutable::class, true);

        self::assertSame(\DateTimeImmutable::class, $binding->concrete());
        self::assertTrue($binding->isShared());
    }

    public function testContainerReportsRegisteredBinding(): void
    {
        $container = new Container();

        self::assertFalse($container->has(\DateTimeInterface::class));

        $container->bind(\DateTimeInterface::class, \DateTimeImmutable::class);

        self::assertTrue($container->has(\DateTimeInterface::class));
    }

    public function testContainerReportsSingletonBinding(): void
    {
        $container = new Container();

        $container->singleton(\DateTimeInterface::class, \DateTimeImmutable::class);

        self::assertTrue($container->has(\DateTimeInterface::class));
    }

    public function testContainerResolvesRegisteredBinding(): void
    {
        $container = new Container();
        $container->bind(\DateTimeInterface::class, \DateTimeImmutable::class);

        $resolved = $container->make(\DateTimeInterface::class);

        self::assertInstanceOf(\DateTimeImmutable::class, $resolved);
    }

    public function testContainerResolvesConcreteClassWithoutBinding(): void
    {
        $container = new Container();

        $resolved = $container->make(\DateTimeImmutable::class);

        self::assertInstanceOf(\DateTimeImmutable::class, $resolved);
    }

    public function testContainerThrowsCoreExceptionWhenClassCannotBeResolved(): void
    {
        $container = new Container();

        $this->expectException(ContainerException::class);

        $container->make('Demezio\\Finbase\\Tests\\UnknownClass');
    }
}
