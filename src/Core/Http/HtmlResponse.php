<?php

declare(strict_types=1);

namespace Demezio\Finbase\Core\Http;

/**
 * Resposta HTTP cujo conteúdo é HTML codificado em UTF-8.
 */
final class HtmlResponse extends Response
{
    public function __construct(string $content, int $status = 200)
    {
        parent::__construct(
            content: $content,
            status: $status,
            headers: ['Content-Type' => 'text/html; charset=UTF-8'],
        );
    }
}
