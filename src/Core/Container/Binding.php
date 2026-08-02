<?php

declare(strict_types=1);

namespace Demezio\Finbase\Core\Container;

/**
 * Representa o registro de uma abstração no contêiner.
 *
 * Armazena a classe concreta associada à abstração e define se a instância
 * resultante deve ser compartilhada entre as resoluções.
 */
final class Binding
{
    /**
     * @param class-string $concrete
     * @param bool $shared Define se a instância resolvida deve ser reutilizada.
     */
    public function __construct(
        private readonly string $concrete,
        private readonly bool $shared = false
    ) {
    }

    /**
     * Retorna a classe concreta associada a este registro.
     *
     * @return class-string
     */
    public function concrete(): string
    {
        return $this->concrete;
    }

    /**
     * Informa se o contêiner deve compartilhar a instância deste registro.
     */
    public function isShared(): bool
    {
        return $this->shared;
    }
}
