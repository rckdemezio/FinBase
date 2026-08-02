<?php

declare(strict_types=1);

namespace Demezio\Finbase\Core\Exceptions;

/**
 * Indica que uma dependência foi solicitada novamente durante sua própria resolução.
 */
final class CircularDependencyException extends ContainerException
{
    /**
     * @param list<class-string> $path
     */
    public function __construct(array $path)
    {
        parent::__construct(sprintf('Circular dependency detected: %s', implode(' -> ', $path)));
    }
}
