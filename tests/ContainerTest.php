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
        $binding = new Binding(TestDependency::class, true);

        self::assertSame(TestDependency::class, $binding->concrete());
        self::assertTrue($binding->isShared());
    }

    public function testContainerReportsRegisteredBinding(): void
    {
        $container = new Container();

        self::assertFalse($container->has(TestContract::class));

        $container->bind(TestContract::class, TestDependency::class);

        self::assertTrue($container->has(TestContract::class));
    }

    public function testContainerReportsSingletonBinding(): void
    {
        $container = new Container();

        $container->singleton(TestContract::class, TestDependency::class);

        self::assertTrue($container->has(TestContract::class));
    }

    public function testContainerResolvesRegisteredBinding(): void
    {
        $container = new Container();
        $container->bind(TestContract::class, TestDependency::class);

        $resolved = $container->make(TestContract::class);

        self::assertInstanceOf(TestDependency::class, $resolved);
    }

    public function testContainerReusesSingletonInstance(): void
    {
        $container = new Container();
        $container->singleton(TestContract::class, TestDependency::class);

        $first = $container->make(TestContract::class);
        $second = $container->make(TestContract::class);

        self::assertSame($first, $second);
    }

    public function testContainerReturnsTheReadyInstanceRegisteredByTheCompositionRoot(): void
    {
        $container = new Container();
        $dependency = new TestDependency();

        $container->instance(TestContract::class, $dependency);

        self::assertTrue($container->has(TestContract::class));
        self::assertSame($dependency, $container->make(TestContract::class));
        self::assertSame($dependency, $container->make(TestContract::class));
    }

    public function testContainerCreatesNewInstanceForTransientBinding(): void
    {
        $container = new Container();
        $container->bind(TestContract::class, TestDependency::class);

        $first = $container->make(TestContract::class);
        $second = $container->make(TestContract::class);

        self::assertNotSame($first, $second);
    }

    public function testContainerResolvesConcreteClassWithoutBinding(): void
    {
        $container = new Container();

        $resolved = $container->make(TestDependency::class);

        self::assertInstanceOf(TestDependency::class, $resolved);
    }

    public function testContainerResolvesConstructorDependenciesRecursively(): void
    {
        $container = new Container();
        $container->bind(TestContract::class, TestDependency::class);

        $service = $container->make(TestService::class);

        self::assertInstanceOf(TestService::class, $service);
        self::assertInstanceOf(TestDependency::class, $service->dependency);
    }

    public function testContainerThrowsCoreExceptionForBuiltInConstructorType(): void
    {
        $container = new Container();

        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage('tipo interno "string"');

        $container->make(ServiceWithBuiltInParameter::class);
    }

    public function testContainerThrowsCoreExceptionWhenClassCannotBeResolved(): void
    {
        $container = new Container();

        $this->expectException(ContainerException::class);

        $container->make('Demezio\\Finbase\\Tests\\UnknownClass');
    }
}

interface TestContract
{
}

final class TestDependency implements TestContract
{
}

final class TestService
{
    public function __construct(public TestContract $dependency)
    {
    }
}

final class ServiceWithBuiltInParameter
{
    public function __construct(string $path)
    {
    }
}
