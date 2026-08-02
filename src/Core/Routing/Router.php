<?php

declare(strict_types=1);

namespace Demezio\Finbase\Core\Routing;

use Demezio\Finbase\Core\Http\JsonResponse;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\Http\Response;

/**
 * Registra rotas estáticas e encaminha requisições ao respectivo manipulador.
 */
final class Router
{
    /** @var list<Route> */
    private array $routes = [];

    /**
     * @param callable(Request): Response $handler
     */
    public function get(string $path, callable $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    /**
     * @param callable(Request): Response $handler
     */
    public function post(string $path, callable $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    /**
     * @param callable(Request): Response $handler
     */
    public function add(string $method, string $path, callable $handler): void
    {
        $this->routes[] = new Route($method, $path, $handler);
    }

    public function dispatch(Request $request): Response
    {
        foreach ($this->routes as $route) {
            if ($route->matches($request)) {
                return $route->handle($request);
            }
        }

        return new JsonResponse(['message' => 'Not Found'], 404);
    }
}
