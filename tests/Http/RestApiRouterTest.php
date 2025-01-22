<?php

namespace Tests\Http;

use ReflectionClass;
use MiniCore\Http\Request;
use MiniCore\API\RestApiRouter;
use PHPUnit\Framework\TestCase;
use MiniCore\API\EndpointInterface;
use MiniCore\Tests\Http\Stub\TestEndpoint;
use MiniCore\Tests\Http\Stub\TestMiddleware;

/**
 * Class RestApiRouterTest
 *
 * Unit tests for the RestApiRouter class, verifying functionality for
 * route registration, request handling, middleware application, and error handling.
 */
class RestApiRouterTest extends TestCase
{
    /**
     * Reset the internal state of the RestApiRouter before each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $reflection = new ReflectionClass(RestApiRouter::class);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routesProperty->setValue([]);
    }

    /**
     * Test route registration in the RestApiRouter.
     *
     * Verifies that the route is correctly added to the internal routes array.
     *
     * @return void
     */
    public function testRegisterRoute(): void
    {
        $endpoint = new TestEndpoint();

        RestApiRouter::register('GET', '/test', $endpoint);

        $routes = RestApiRouter::getRoutes();
        $this->assertArrayHasKey('GET', $routes);
        $this->assertArrayHasKey('/test', $routes['GET']);
        $this->assertSame($endpoint, $routes['GET']['/test']['endpoint']);
    }

    /**
     * Test handling of a registered route.
     *
     * Ensures that the router correctly processes a request and returns the expected response.
     *
     * @return void
     */
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

    /**
     * Test handling of a route with middleware.
     *
     * Ensures that middleware is applied correctly during request handling.
     *
     * @return void
     */
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

    /**
     * Test handling of a non-existent route.
     *
     * Verifies that the router returns a 404 error for a route that is not registered.
     *
     * @return void
     */
    public function testHandleRequestRouteNotFound(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->method('getMethod')->willReturn('GET');
        $requestMock->method('getPath')->willReturn('/not-found');

        $response = RestApiRouter::handle($requestMock);

        $this->assertEquals(['error' => 'Method not allowed'], $response);
        $this->assertEquals(405, http_response_code());
    }

    /**
     * Test handling of a route with an unsupported HTTP method.
     *
     * Verifies that the router returns a 405 error when a method is not allowed for a route.
     *
     * @return void
     */
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

    /**
     * Test registration of a route with invalid middleware.
     *
     * Ensures that an exception is thrown if the middleware does not implement MiddlewareInterface.
     *
     * @return void
     * @throws \RuntimeException When middleware is invalid.
     */
    public function testRegisterWithInvalidMiddleware(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('All middleware must implement MiddlewareInterface');

        $endpointMock = $this->createMock(EndpointInterface::class);

        RestApiRouter::register('GET', '/test', $endpointMock, ['InvalidMiddleware']);
    }
}
