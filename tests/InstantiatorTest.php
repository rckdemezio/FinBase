<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests\Core\Container;

use Demezio\Finbase\Core\Container\Instantiator;
use PHPUnit\Framework\TestCase;

final class InstantiatorTest extends TestCase
{
    public function testInstantiatesClassWithResolvedArguments(): void
    {
        $reflection = new \ReflectionClass(InstantiableClass::class);
        $argument = new ResolvedArgument();

        $instance = (new Instantiator())->instantiate($reflection, [$argument]);

        self::assertInstanceOf(InstantiableClass::class, $instance);
        self::assertSame($argument, $instance->argument);
    }
}

final class InstantiableClass
{
    public function __construct(public ResolvedArgument $argument)
    {
    }
}

final class ResolvedArgument
{
}
