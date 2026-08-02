<?php

declare(strict_types=1);

namespace Demezio\Finbase\Core\Contracts;

use Demezio\Finbase\Core\Exceptions\ContainerException;
use Demezio\Finbase\Core\Exceptions\NotFoundBindingException;
use Psr\Container\ContainerInterface as PsrContainerInterface;

/**
 * Define o contrato de um contêiner de injeção de dependências.
 *
 * O contêiner associa uma abstração — normalmente uma interface ou uma classe —
 * à classe concreta que deve ser instanciada quando essa abstração for solicitada.
 * Também permite registrar serviços compartilhados como instâncias únicas.
 */
interface ContainerInterface extends PsrContainerInterface
{
    /**
     * Registra uma dependência transitória no contêiner.
     *
     * A cada resolução da abstração, o contêiner deve fornecer uma nova instância
     * da classe concreta. Use este método para serviços que não precisam manter
     * estado entre usos.
     *
     * Exemplo: associar `TransactionRepositoryInterface` a
     * `JsonTransactionRepository`.
     *
     * @param class-string $abstract
     * @param class-string $concrete
     */
    public function bind(string $abstract, string $concrete): void;

    /**
     * Registra uma dependência compartilhada como singleton.
     *
     * Após a primeira resolução, o contêiner deve reutilizar a mesma instância
     * para as próximas solicitações da abstração. É indicado para objetos cujo
     * estado deve ser compartilhado durante o ciclo de vida da aplicação, como
     * um logger ou uma configuração.
     *
     * @param class-string $abstract
     * @param class-string $concrete
     */
    public function singleton(string $abstract, string $concrete): void;

    /**
     * Registra uma instância já construída pelo Composition Root.
     *
     * Diferentemente de {@see singleton()}, o contêiner não constrói este
     * objeto: apenas o devolve nas resoluções posteriores da abstração.
     *
     * @param class-string $abstract
     */
    public function instance(string $abstract, object $instance): void;

    /**
     * Resolve uma abstração registrada ou uma classe concreta.
     *
     * Para registros feitos com {@see singleton()} ou {@see instance()}, as
     * chamadas seguintes devem retornar a mesma instância. Para registros feitos
     * com {@see bind()}, a implementação deve criar uma nova instância a cada
     * resolução.
     *
     * @param class-string $abstract
     *
     * @throws ContainerException Caso a classe não possa ser resolvida.
     */
    public function make(string $abstract): object;

    /**
     * Resolve uma entrada pela API compatível com PSR-11.
     *
     * @throws NotFoundBindingException Caso não exista uma entrada recuperável
     *                                  para o identificador solicitado.
     * @throws ContainerException Caso a entrada não possa ser resolvida.
     */
    public function get(string $id): object;

    /**
     * Verifica se o contêiner pode recuperar uma entrada pelo identificador.
     */
    public function has(string $id): bool;
}
