<?php

declare(strict_types=1);

namespace Demezio\Finbase\Core\Http;

/**
 * Resposta HTTP cujo conteúdo é uma representação JSON.
 */
final class JsonResponse extends Response
{
    /**
     * @param array<string, string> $headers
     *
     * @throws \JsonException
     */
    public function __construct(mixed $data, int $status = 200, array $headers = [])
    {
        parent::__construct(
            json_encode($data, JSON_THROW_ON_ERROR),
            $status,
            array_merge($headers, ['Content-Type' => 'application/json']),
        );
    }
}
