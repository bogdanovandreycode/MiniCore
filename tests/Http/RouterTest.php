<?php

namespace MiniCore\Tests\Http;

use PHPUnit\Framework\TestCase;
use MiniCore\Http\Router;
use MiniCore\Http\Request;
use MiniCore\Http\RouteInterface;

/**
 * Unit tests for the Router class.
 *
 * This test suite verifies the correct functionality of the Router class,
 * ensuring that routes are properly registered, handled, and normalized.
 *
 * Covered functionality:
 * - Registering routes with HTTP methods and paths.
 * - Handling requests with registered routes.
 * - Handling requests with unregistered routes.
 * - Handling requests with unsupported HTTP methods.
 * - Normalizing route paths.
 */
class RouterTest extends TestCase
{
    /**
     * Clears registered routes before each test.
     */
    protected function setUp(): void
    {
        $reflection = new \ReflectionClass(Router::class);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routesProperty->setValue([]);
    }

    /**
     * Tests registering a new route.
     */
    public function testRegisterRoute()
    {
        $endpointMock = $this->createMock(RouteInterface::class);
        Router::register('/test', $endpointMock);
        $routes = Router::getRoutes();
        $this->assertSame($endpointMock, $routes['/test'], 'Registered endpoint should match.');
    }

    /**
     * Tests handling a request with a registered route.
     */
    public function testHandleRequestWithRegisteredRoute()
    {
        $endpointMock = $this->createMock(RouteInterface::class);
        $endpointMock->expects($this->once())
            ->method('handle')
            ->with(['param' => 'value'])
            ->willReturn('Handled Response');

        Router::register('/test', $endpointMock);

        $requestMock = $this->createMock(Request::class);
        $requestMock->method('getPath')->willReturn('/test');
        $requestMock->method('getQueryParams')->willReturn(['param' => 'value']);

        $response = Router::handle($requestMock);

        $this->assertEquals('Handled Response', $response, 'Response should match the handler output.');
    }

    /**
     * Tests handling a request with an unregistered route.
     */
    public function testHandleRequestWithUnregisteredRoute()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Route not found');
        $this->expectExceptionCode(404);

        $requestMock = $this->createMock(Request::class);
        $requestMock->method('getPath')->willReturn('/not-registered');

        Router::handle($requestMock);
    }

    /**
     * Tests handling a request with an unsupported HTTP method.
     */
    public function testHandleRequestWithUnsupportedMethod()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Route not found');
        $this->expectExceptionCode(404);

        $requestMock = $this->createMock(Request::class);
        $requestMock->method('getMethod')->willReturn('DELETE');
        $requestMock->method('getPath')->willReturn('/test');

        Router::handle($requestMock);
    }

    /**
     * Tests route path normalization.
     */
    public function testRouteNormalization()
    {
        $reflection = new \ReflectionClass(Router::class);
        $method = $reflection->getMethod('normalizePath');
        $method->setAccessible(true);

        $this->assertEquals('/api/user', $method->invoke(null, '/api/user/'), 'Trailing slash should be removed.');
        $this->assertEquals('/api/user', $method->invoke(null, 'api/user'), 'Missing leading slash should be added.');
        $this->assertEquals('/api/user', $method->invoke(null, '\\api\\user'), 'Backslashes should be normalized.');
        $this->assertEquals('/api/user', $method->invoke(null, '//api//user//'), 'Multiple slashes should be collapsed.');
    }
}
