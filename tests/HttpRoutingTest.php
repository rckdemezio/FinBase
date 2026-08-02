<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests;

use Demezio\Finbase\Core\Http\JsonResponse;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\Http\Response;
use Demezio\Finbase\Core\Routing\Router;
use PHPUnit\Framework\TestCase;

final class HttpRoutingTest extends TestCase
{
    public function testRequestEncapsulatesHttpData(): void
    {
        $request = new Request('post', '/accounts?currency=BRL', ['currency' => 'BRL'], '{"name":"Principal"}');

        self::assertSame('POST', $request->method());
        self::assertSame('/accounts?currency=BRL', $request->uri());
        self::assertSame('/accounts', $request->path());
        self::assertSame(['currency' => 'BRL'], $request->query());
        self::assertSame('{"name":"Principal"}', $request->body());
    }

    public function testJsonResponseEncodesDataAndDefinesContentType(): void
    {
        $response = new JsonResponse(['status' => 'ok']);

        self::assertSame(200, $response->status());
        self::assertSame('application/json', $response->headers()['Content-Type']);
        self::assertSame(['status' => 'ok'], json_decode($response->content(), true, 512, JSON_THROW_ON_ERROR));
    }

    public function testRouterDispatchesTheMatchingRoute(): void
    {
        $router = new Router();
        $router->get('/health', static fn (Request $request): Response => new JsonResponse(['status' => 'ok']));

        $response = $router->dispatch(new Request('GET', '/health'));

        self::assertSame(200, $response->status());
        self::assertSame(['status' => 'ok'], json_decode($response->content(), true, 512, JSON_THROW_ON_ERROR));
    }

    public function testRouterReturnsNotFoundForAnUnknownRoute(): void
    {
        $response = (new Router())->dispatch(new Request('GET', '/unknown'));

        self::assertSame(404, $response->status());
        self::assertSame(['message' => 'Not Found'], json_decode($response->content(), true, 512, JSON_THROW_ON_ERROR));
    }
}
