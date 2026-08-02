<?php

declare(strict_types=1);

namespace Demezio\Finbase\Core\Container;

use Demezio\Finbase\Core\Contracts\ContainerInterface;

/**
 * Implementação em memória do registro de dependências.
 *
 * Cada abstração é usada como chave para o seu respectivo {@see Binding}.
 * Um novo registro para a mesma abstração substitui o anterior.
 */
final class Container implements ContainerInterface
{
    /** @var array<string, Binding> */
    private array $bindings = [];

    /**
     * Registra uma dependência transitória.
     */
    public function bind(string $abstract, string $concrete): void
    {
        $this->bindings[$abstract] = new Binding(
            concrete: $concrete,
            shared: false
        );
    }

    /**
     * Registra ou substitui uma dependência compartilhada.
     */
    public function singleton(string $abstract, string $concrete): void
    {
        $this->bindings[$abstract] = new Binding(
            concrete: $concrete,
            shared: true
        );
    }

    /**
     * Verifica a existência de um registro para a abstração informada.
     */
    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]);
    }

    /**
     * Ainda não resolve dependências.
     *
     * @throws \LogicException Enquanto a resolução de dependências não for implementada.
     */
    public function make(string $abstract): object
    {
        throw new \LogicException('Not implemented yet.');
    }
}
