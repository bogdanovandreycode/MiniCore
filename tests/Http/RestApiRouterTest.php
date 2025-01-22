<?php

namespace Tests\Http;

use ReflectionClass;
use MiniCore\Http\Request;
use MiniCore\API\RestApiRouter;
use PHPUnit\Framework\TestCase;
use MiniCore\API\EndpointInterface;
use MiniCore\Tests\Http\Stub\TestEndpoint;
use MiniCore\Tests\Http\Stub\TestMiddleware;

class RestApiRouterTest extends TestCase
{
    protected function setUp(): void
    {
        $reflection = new ReflectionClass(RestApiRouter::class);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routesProperty->setValue([]);
    }

    public function testRegisterRoute(): void
    {
        $endpoint = new TestEndpoint();

        RestApiRouter::register('GET', '/test', $endpoint);

        $routes = RestApiRouter::getRoutes();
        $this->assertArrayHasKey('GET', $routes);
        $this->assertArrayHasKey('/test', $routes['GET']);
        $this->assertSame($endpoint, $routes['GET']['/test']['endpoint']);
    }

    public function testHandleRequest(): void
    {
        $endpoint = new TestEndpoint();

        RestApiRouter::register('GET', '/api/test', $endpoint);

        $requestMock = $this->createMock(Request::class);
        $requestMock->method('getMethod')->willReturn('GET');
        $requestMock->method('getPath')->willReturn('/api/test');
        $requestMock->method('getQueryParams')->willReturn([]);

        $response = RestApiRouter::handle($requestMock);

        $this->assertSame(['status' => 'success'], $response);
    }

    public function testHandleRequestWithMiddleware(): void
    {
        $middleware = new TestMiddleware();
        $endpoint = new TestEndpoint();

        RestApiRouter::register('GET', '/api/test', $endpoint, [$middleware]);

        $requestMock = $this->createMock(Request::class);
        $requestMock->method('getMethod')->willReturn('GET');
        $requestMock->method('getPath')->willReturn('/api/test');
        $requestMock->method('getQueryParams')->willReturn([]);
        $response = RestApiRouter::handle($requestMock);

        $this->assertSame(['status' => 'success'], $response);
    }


    public function testHandleRequestRouteNotFound(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->method('getMethod')->willReturn('GET');
        $requestMock->method('getPath')->willReturn('/not-found');

        $response = RestApiRouter::handle($requestMock);

        $this->assertEquals(['error' => 'Method not allowed'], $response);
        $this->assertEquals(405, http_response_code());
    }

    public function testHandleRequestMethodNotAllowed(): void
    {
        $endpointMock = $this->createMock(EndpointInterface::class);
        RestApiRouter::register('POST', '/test', $endpointMock);

        $requestMock = $this->createMock(Request::class);
        $requestMock->method('getMethod')->willReturn('GET');
        $requestMock->method('getPath')->willReturn('/test');

        $response = RestApiRouter::handle($requestMock);

        $this->assertEquals(['error' => 'Method not allowed'], $response);
        $this->assertEquals(405, http_response_code());
    }

    public function testRegisterWithInvalidMiddleware(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('All middleware must implement MiddlewareInterface');

        $endpointMock = $this->createMock(EndpointInterface::class);

        RestApiRouter::register('GET', '/test', $endpointMock, ['InvalidMiddleware']);
    }
}
