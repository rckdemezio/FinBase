<?php

declare(strict_types=1);

namespace Demezio\Finbase\Core\Container;

use Demezio\Finbase\Core\Contracts\ContainerInterface;
use Demezio\Finbase\Core\Exceptions\ContainerException;

/**
 * Implementação em memória de um contêiner de dependências.
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

    /** {@inheritDoc} */
    public function make(string $abstract): object
    {
        $concrete = $this->has($abstract)
            ? $this->bindings[$abstract]->concrete()
            : $abstract;

        return $this->instantiate($concrete);
    }

    /**
     * Instancia uma classe concreta que não possui dependências no construtor.
     *
     * @param class-string $concrete
     *
     * @throws ContainerException Caso a classe não exista, não seja instanciável
     *                            ou exija dependências no construtor.
     */
    private function instantiate(string $concrete): object
    {
        try {
            $reflection = new \ReflectionClass($concrete);

            if (! $reflection->isInstantiable()) {
                throw new ContainerException(sprintf('A classe "%s" não pode ser instanciada.', $concrete));
            }

            return $reflection->newInstance();
        } catch (ContainerException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw new ContainerException(
                sprintf('Não foi possível resolver "%s".', $concrete),
                previous: $exception
            );
        }
    }
}
