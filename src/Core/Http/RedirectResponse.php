<?php

declare(strict_types=1);

namespace Demezio\Finbase\Core\Http;

/**
 * Resposta HTTP que direciona o cliente para outra localização.
 */
final class RedirectResponse extends Response
{
    public function __construct(string $location, int $status = 302)
    {
        parent::__construct(
            content: '',
            status: $status,
            headers: ['Location' => $location],
        );
    }
}
