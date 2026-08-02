<?php

declare(strict_types=1);

namespace Demezio\Finbase\Tests;

use Demezio\Finbase\Core\Http\InvalidJsonException;
use Demezio\Finbase\Core\Http\JsonResponse;
use Demezio\Finbase\Core\Http\RedirectResponse;
use Demezio\Finbase\Core\Http\Request;
use Demezio\Finbase\Core\Http\Response;
use Demezio\Finbase\Core\Routing\Router;
use PHPUnit\Framework\TestCase;

final class HttpRoutingTest extends TestCase
{
    public function testRequestEncapsulatesHttpData(): void
    {
        $request = new Request('post', '/accounts?currency=BRL', ['currency' => 'BRL'], '{"name":"Principal"}', form: ['name' => 'Principal']);

        self::assertSame('POST', $request->method());
        self::assertSame('/accounts?currency=BRL', $request->uri());
        self::assertSame('/accounts', $request->path());
        self::assertSame(['currency' => 'BRL'], $request->query());
        self::assertSame('{"name":"Principal"}', $request->body());
        self::assertSame(['name' => 'Principal'], $request->form());
        self::assertSame(['name' => 'Principal'], $request->json());
    }

    public function testRequestThrowsASemanticExceptionForInvalidJson(): void
    {
        $request = new Request('POST', '/accounts', body: '{');

        $this->expectException(InvalidJsonException::class);

        $request->json();
    }

    public function testJsonResponseEncodesDataAndDefinesContentType(): void
    {
        $response = new JsonResponse(['status' => 'ok']);

        self::assertSame(200, $response->status());
        self::assertSame('application/json', $response->headers()['Content-Type']);
        self::assertSame(['status' => 'ok'], json_decode($response->content(), true, 512, JSON_THROW_ON_ERROR));
    }

    public function testRedirectResponseDefinesTheDestinationAndDefaultStatus(): void
    {
        $response = new RedirectResponse('/accounts');

        self::assertSame(302, $response->status());
        self::assertSame('', $response->content());
        self::assertSame('/accounts', $response->headers()['Location']);
    }

    public function testRouterDispatchesTheMatchingRoute(): void
    {
        $router = new Router();
        $router->get('/health', static fn (Request $request): Response => new JsonResponse(['status' => 'ok']));

        $response = $router->dispatch(new Request('GET', '/health'));

        self::assertSame(200, $response->status());
        self::assertSame(['status' => 'ok'], json_decode($response->content(), true, 512, JSON_THROW_ON_ERROR));
    }

    public function testRouterExtractsDynamicRouteParameters(): void
    {
        $router = new Router();
        $router->get('/accounts/{id}', static fn (Request $request): Response => new JsonResponse([
            'id' => $request->routeParameter('id'),
        ]));

        $response = $router->dispatch(new Request('GET', '/accounts/550e8400-e29b-41d4-a716-446655440000'));

        self::assertSame(
            ['id' => '550e8400-e29b-41d4-a716-446655440000'],
            json_decode($response->content(), true, 512, JSON_THROW_ON_ERROR),
        );
    }

    public function testRouterReturnsNotFoundForAnUnknownRoute(): void
    {
        $response = (new Router())->dispatch(new Request('GET', '/unknown'));

        self::assertSame(404, $response->status());
        self::assertSame(['message' => 'Not Found'], json_decode($response->content(), true, 512, JSON_THROW_ON_ERROR));
    }

    public function testApiRoutesRegisterTheAccountsListing(): void
    {
        $response = $this->routerWithRegisteredRoutes()->dispatch(new Request('GET', '/api/accounts'));

        self::assertSame(200, $response->status());
        self::assertSame('application/json', $response->headers()['Content-Type']);
    }

    public function testWebRoutesRegisterTheAccountsListing(): void
    {
        $response = $this->routerWithRegisteredRoutes()->dispatch(new Request('GET', '/accounts'));

        self::assertSame(200, $response->status());
        self::assertSame('text/html; charset=UTF-8', $response->headers()['Content-Type']);
        self::assertStringContainsString('<title>Contas · FinBase</title>', $response->content());
    }

    private function routerWithRegisteredRoutes(): Router
    {
        /** @var Demezio\Finbase\Core\Contracts\ContainerInterface $container */
        $container = require dirname(__DIR__).'/bootstrap/app.php';
        $router = new Router();

        /** @var callable(Router, Demezio\Finbase\Core\Contracts\ContainerInterface): void $registerApiRoutes */
        $registerApiRoutes = require dirname(__DIR__).'/routes/api.php';
        $registerApiRoutes($router, $container);

        /** @var callable(Router, Demezio\Finbase\Core\Contracts\ContainerInterface): void $registerWebRoutes */
        $registerWebRoutes = require dirname(__DIR__).'/routes/web.php';
        $registerWebRoutes($router, $container);

        return $router;
    }
}
