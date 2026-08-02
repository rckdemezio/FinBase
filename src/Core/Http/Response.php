<?php

declare(strict_types=1);

namespace Demezio\Finbase\Core\Http;

/**
 * Representa uma resposta HTTP que pode ser enviada pelo front controller.
 */
class Response
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        private readonly string $content = '',
        private readonly int $status = 200,
        private readonly array $headers = [],
    ) {
    }

    public function content(): string
    {
        return $this->content;
    }

    public function status(): int
    {
        return $this->status;
    }

    /**
     * @return array<string, string>
     */
    public function headers(): array
    {
        return $this->headers;
    }

    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header(sprintf('%s: %s', $name, $value));
        }

        echo $this->content;
    }
}
