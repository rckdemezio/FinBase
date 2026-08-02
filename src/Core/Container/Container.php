<?php

declare(strict_types=1);

namespace Demezio\Finbase\Core\Container;

use Demezio\Finbase\Core\Contracts\ContainerInterface;
use Demezio\Finbase\Core\Exceptions\ContainerException;

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

        try {
            $reflection = new \ReflectionClass($concrete);

            if (! $reflection->isInstantiable()) {
                throw new ContainerException(sprintf('A classe "%s" não pode ser instanciada.', $concrete));
            }

            $constructor = $reflection->getConstructor();
            $arguments = $constructor === null || $constructor->getNumberOfParameters() === 0
                ? []
                : $this->resolveArguments($constructor);

            return $this->instantiator->instantiate($reflection, $arguments);
        } catch (ContainerException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw new ContainerException(
                sprintf('Não foi possível resolver "%s".', $concrete),
                previous: $exception
            );
        }
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
