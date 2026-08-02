<?php

declare(strict_types=1);

namespace Demezio\Finbase\Core\Routing;

use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\Http\Response;

/**
 * Representa uma associação entre método, caminho e manipulador HTTP.
 */
final class Route
{
    /** @var callable(Request): Response */
    private $handler;

    /**
     * @param callable(Request): Response $handler
     */
    public function __construct(
        private readonly string $method,
        private readonly string $path,
        callable $handler,
    ) {
        $this->handler = $handler;
    }

    /**
     * @return array<string, string>|null Os parâmetros extraídos ou null quando não há correspondência.
     */
    public function match(Request $request): ?array
    {
        if (strtoupper($this->method) !== $request->method()) {
            return null;
        }

        $parameters = [];
        $pattern = $this->pattern();

        if (preg_match($pattern, $request->path(), $matches) !== 1) {
            return null;
        }

        foreach ($matches as $name => $value) {
            if (is_string($name)) {
                $parameters[$name] = rawurldecode($value);
            }
        }

        return $parameters;
    }

    public function handle(Request $request): Response
    {
        return ($this->handler)($request);
    }

    private function pattern(): string
    {
        $pattern = '';
        $offset = 0;

        preg_match_all('/\{([a-zA-Z][a-zA-Z0-9_]*)\}/', $this->path, $placeholders, PREG_OFFSET_CAPTURE);

        foreach ($placeholders[0] as $index => [$placeholder, $position]) {
            $name = $placeholders[1][$index][0];
            $pattern .= preg_quote(substr($this->path, $offset, $position - $offset), '#');
            $pattern .= sprintf('(?P<%s>[^/]+)', $name);
            $offset = $position + strlen($placeholder);
        }

        return '#^'. $pattern .preg_quote(substr($this->path, $offset), '#').'$#D';
    }
}
