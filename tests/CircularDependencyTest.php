<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Core\Container;

use Demezio\Finbase\Core\Container\Container;
use Demezio\Finbase\Core\Exceptions\CircularDependencyException;
use Demezio\Finbase\Core\Exceptions\ContainerException;
use PHPUnit\Framework\TestCase;

final class CircularDependencyTest extends TestCase
{
    public function testDetectsDirectCircularDependency(): void
    {
        $container = new Container();

        try {
            $container->make(DirectCycle::class);
            self::fail('Era esperada uma exceção de dependência circular.');
        } catch (CircularDependencyException $exception) {
            self::assertSame(
                sprintf('Circular dependency detected: %s -> %s', DirectCycle::class, DirectCycle::class),
                $exception->getMessage()
            );
        }
    }

    public function testDetectsIndirectCircularDependency(): void
    {
        $container = new Container();

        try {
            $container->make(IndirectCycleA::class);
            self::fail('Era esperada uma exceção de dependência circular.');
        } catch (CircularDependencyException $exception) {
            self::assertSame(
                sprintf(
                    'Circular dependency detected: %s -> %s -> %s',
                    IndirectCycleA::class,
                    IndirectCycleB::class,
                    IndirectCycleA::class
                ),
                $exception->getMessage()
            );
        }
    }

    public function testCleansResolutionStackAfterFailure(): void
    {
        $container = new Container();

        try {
            $container->make(ServiceWithUnsupportedParameter::class);
        } catch (ContainerException) {
        }

        $resolved = $container->make(ResolvableService::class);

        self::assertInstanceOf(ResolvableService::class, $resolved);
    }

    public function testAllowsRepeatedDependencyOutsideActiveResolutionPath(): void
    {
        $container = new Container();

        $resolved = $container->make(ServiceWithRepeatedDependency::class);

        self::assertInstanceOf(ServiceWithRepeatedDependency::class, $resolved);
        self::assertInstanceOf(SharedDependency::class, $resolved->dependency);
        self::assertInstanceOf(RepositoryWithSharedDependency::class, $resolved->repository);
        self::assertInstanceOf(SharedDependency::class, $resolved->repository->dependency);
    }
}

final class DirectCycle
{
    public function __construct(DirectCycle $dependency)
    {
    }
}

final class IndirectCycleA
{
    public function __construct(IndirectCycleB $dependency)
    {
    }
}

final class IndirectCycleB
{
    public function __construct(IndirectCycleA $dependency)
    {
    }
}

final class ServiceWithUnsupportedParameter
{
    public function __construct(string $value)
    {
    }
}

final class ResolvableService
{
}

final class SharedDependency
{
}

final class RepositoryWithSharedDependency
{
    public function __construct(public SharedDependency $dependency)
    {
    }
}

final class ServiceWithRepeatedDependency
{
    public function __construct(
        public SharedDependency $dependency,
        public RepositoryWithSharedDependency $repository
    ) {
    }
}
