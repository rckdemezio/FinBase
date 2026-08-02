<?php

declare(strict_types=1);

namespace Demezio\Finbase\Core\View;

/**
 * Renderiza templates PHP a partir de um diretório base configurado.
 */
final class View
{
    public function __construct(private readonly string $basePath)
    {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): string
    {
        $path = $this->basePath.'/'.$template.'.php';

        extract($data, EXTR_SKIP);

        ob_start();

        require $path;

        return (string) ob_get_clean();
    }
}
