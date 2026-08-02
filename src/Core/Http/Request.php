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
     * @param array<string, mixed> $form
     * @param array<string, string> $routeParameters
     */
    public function __construct(
        private readonly string $method,
        private readonly string $uri,
        private readonly array $query = [],
        private readonly string $body = '',
        private readonly array $routeParameters = [],
        private readonly array $form = [],
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
            form: $_POST,
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

    /**
     * @return array<string, string>
     */
    public function routeParameters(): array
    {
        return $this->routeParameters;
    }

    public function routeParameter(string $name): ?string
    {
        return $this->routeParameters[$name] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function form(): array
    {
        return $this->form;
    }

    /**
     * @param array<string, string> $routeParameters
     */
    public function withRouteParameters(array $routeParameters): self
    {
        return new self($this->method, $this->uri, $this->query, $this->body, $routeParameters, $this->form);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws InvalidJsonException
     */
    public function json(): array
    {
        try {
            $data = json_decode($this->body, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new InvalidJsonException('O corpo da requisição contém JSON inválido.', previous: $exception);
        }

        if (! is_array($data) || array_is_list($data)) {
            throw new InvalidJsonException('O corpo da requisição deve conter um objeto JSON.');
        }

        return $data;
    }
}
