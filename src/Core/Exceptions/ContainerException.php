<?php

declare(strict_types=1);

namespace Demezio\Finbase\Core\Exceptions;

use Psr\Container\ContainerExceptionInterface;

/**
 * Indica uma falha durante a resolução de uma dependência.
 */
class ContainerException extends \RuntimeException implements ContainerExceptionInterface
{
}
