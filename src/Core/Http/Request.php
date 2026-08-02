<?php

declare(strict_types=1);

namespace Demezio\Finbase\Core\Http;

/**
 * Representa uma requisição HTTP independente das superglobais do PHP.
 */
final class Request
{
    /**
     * @param array<string, mixed> $query
     */
    public function __construct(
        private readonly string $method,
        private readonly string $uri,
        private readonly array $query = [],
        private readonly string $body = '',
    ) {
    }

    /**
     * Cria uma requisição a partir da fronteira HTTP nativa do PHP.
     */
    public static function fromGlobals(): self
    {
        $body = file_get_contents('php://input');

        return new self(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            $_SERVER['REQUEST_URI'] ?? '/',
            $_GET,
            $body === false ? '' : $body,
        );
    }

    public function method(): string
    {
        return strtoupper($this->method);
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function path(): string
    {
        $path = parse_url($this->uri, PHP_URL_PATH);

        return is_string($path) && $path !== '' ? $path : '/';
    }

    /**
     * @return array<string, mixed>
     */
    public function query(): array
    {
        return $this->query;
    }

    public function body(): string
    {
        return $this->body;
    }
}
