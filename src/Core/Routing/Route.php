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

    public function matches(Request $request): bool
    {
        return strtoupper($this->method) === $request->method()
            && $this->path === $request->path();
    }

    public function handle(Request $request): Response
    {
        return ($this->handler)($request);
    }
}
