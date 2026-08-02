<?php

declare(strict_types=1);

namespace Demezio\Finbase\Core\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

/**
 * Indica que o contêiner não possui uma entrada para o identificador solicitado.
 */
final class NotFoundBindingException extends ContainerException implements NotFoundExceptionInterface
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Nenhuma entrada foi encontrada para "%s".', $id));
    }
}
