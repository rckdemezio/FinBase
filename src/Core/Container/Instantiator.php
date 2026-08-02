<?php

declare(strict_types=1);

namespace Demezio\Finbase\Core\Container;

/**
 * Cria instâncias a partir de metadados e argumentos já resolvidos.
 *
 * Não inspeciona construtores nem resolve dependências. Essas decisões
 * pertencem ao componente que o utiliza.
 */
final class Instantiator
{
    /**
     * Cria uma instância usando os argumentos na ordem do construtor refletido.
     *
     * @param array<int, object> $arguments
     */
    public function instantiate(\ReflectionClass $reflection, array $arguments): object
    {
        return $reflection->newInstanceArgs($arguments);
    }
}
