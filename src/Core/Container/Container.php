<?php

declare(strict_types=1);

namespace Demezio\Finbase\Core\Container;

use Demezio\Finbase\Core\Contracts\ContainerInterface;
use Demezio\Finbase\Core\Exceptions\CircularDependencyException;
use Demezio\Finbase\Core\Exceptions\ContainerException;
use Demezio\Finbase\Core\Exceptions\NotFoundBindingException;

/**
 * Implementação em memória de um contêiner de dependências.
 *
 * Cada abstração é usada como chave para o seu respectivo {@see Binding}.
 * Um novo registro para a mesma abstração substitui o anterior. O contêiner
 * coordena a resolução e delega a criação da instância ao {@see Instantiator}.
 */
final class Container implements ContainerInterface
{
    /** @var array<string, Binding> */
    private array $bindings = [];

    /** @var array<string, object> */
    private array $instances = [];

    /** @var list<class-string> */
    private array $resolving = [];

    private readonly Instantiator $instantiator;

    public function __construct()
    {
        $this->instantiator = new Instantiator();
    }

    /**
     * Registra uma dependência transitória.
     */
    public function bind(string $abstract, string $concrete): void
    {
        $this->bindings[$abstract] = new Binding(
            concrete: $concrete,
            shared: false
        );

        unset($this->instances[$abstract]);
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

        unset($this->instances[$abstract]);
    }

    /**
     * Registra uma instância pronta, criada fora do contêiner.
     */
    public function instance(string $abstract, object $instance): void
    {
        unset($this->bindings[$abstract]);
        $this->instances[$abstract] = $instance;
    }

    /**
     * Verifica a existência de um registro para a abstração informada.
     */
    public function has(string $id): bool
    {
        if (isset($this->bindings[$id]) || isset($this->instances[$id])) {
            return true;
        }

        if (! class_exists($id)) {
            return false;
        }

        try {
            return (new \ReflectionClass($id))->isInstantiable();
        } catch (\ReflectionException) {
            return false;
        }
    }

    /** {@inheritDoc} */
    public function make(string $abstract): object
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $cycleStart = array_search($abstract, $this->resolving, true);

        if ($cycleStart !== false) {
            throw new CircularDependencyException([
                ...array_slice($this->resolving, $cycleStart),
                $abstract,
            ]);
        }

        $this->resolving[] = $abstract;

        $binding = $this->bindings[$abstract] ?? null;
        $concrete = $binding !== null
            ? $binding->concrete()
            : $abstract;

        try {
            $reflection = new \ReflectionClass($concrete);

            if (! $reflection->isInstantiable()) {
                throw new ContainerException(sprintf('A classe "%s" não pode ser instanciada.', $concrete));
            }

            $constructor = $reflection->getConstructor();
            $arguments = $constructor === null || $constructor->getNumberOfParameters() === 0
                ? []
                : $this->resolveArguments($constructor);

            $instance = $this->instantiator->instantiate($reflection, $arguments);

            if ($binding?->isShared()) {
                $this->instances[$abstract] = $instance;
            }

            return $instance;
        } catch (ContainerException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw new ContainerException(
                sprintf('Não foi possível resolver "%s".', $concrete),
                previous: $exception
            );
        } finally {
            array_pop($this->resolving);
        }
    }

    /** {@inheritDoc} */
    public function get(string $id): object
    {
        if (! $this->has($id)) {
            throw new NotFoundBindingException($id);
        }

        return $this->make($id);
    }

    /**
     * Resolve recursivamente os argumentos exigidos pelo construtor.
     *
     * @return array<int, object>
     *
     * @throws ContainerException Caso um parâmetro não possua um tipo de classe
     *                            que o contêiner possa resolver.
     */
    private function resolveArguments(\ReflectionMethod $constructor): array
    {
        $arguments = [];

        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();

            if (! $type instanceof \ReflectionNamedType) {
                throw new ContainerException(
                    sprintf('O parâmetro "$%s" não possui um tipo de classe que possa ser resolvido.', $parameter->getName())
                );
            }

            if ($type->isBuiltin()) {
                throw new ContainerException(
                    sprintf(
                        'O Container não pode resolver o tipo interno "%s" do parâmetro "$%s".',
                        $type->getName(),
                        $parameter->getName()
                    )
                );
            }

            $arguments[] = $this->make($type->getName());
        }

        return $arguments;
    }
}
