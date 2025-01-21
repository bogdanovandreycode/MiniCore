<?php

namespace Tests\Http;

use ReflectionClass;
use MiniCore\Http\Request;
use PHPUnit\Framework\TestCase;
use MiniCore\Http\RestApiRouter;
use MiniCore\API\EndpointInterface;
use MiniCore\API\MiddlewareInterface;

class RestApiRouterTest extends TestCase
{
    protected function setUp(): void
    {
        // Очистим зарегистрированные маршруты перед каждым тестом
        $reflection = new ReflectionClass(RestApiRouter::class);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routesProperty->setValue([]);
    }

    public function testRegisterRoute(): void
    {
        $endpointMock = $this->createMock(EndpointInterface::class);

        RestApiRouter::register('GET', '/test', $endpointMock);

        $routes = RestApiRouter::getRoutes();
        $this->assertArrayHasKey('GET', $routes);
        $this->assertArrayHasKey('/test', $routes['GET']);
        $this->assertSame($endpointMock, $routes['GET']['/test']['endpoint']);
    }

    public function testHandleRequest(): void
    {
        $endpointMock = $this->createMock(EndpointInterface::class);
        $endpointMock->method('process')->willReturn(['success' => true]);

        RestApiRouter::register('GET', '/test', $endpointMock);

        $requestMock = $this->createMock(Request::class);
        $requestMock->method('getMethod')->willReturn('GET');
        $requestMock->method('getPath')->willReturn('/test');
        $requestMock->method('getQueryParams')->willReturn([]);

        $response = RestApiRouter::handle($requestMock);

        $this->assertSame(['success' => true], $response);
    }

    public function testHandleRequestWithMiddleware(): void
    {
        $middlewareMock = $this->createMock(MiddlewareInterface::class);
        $middlewareMock->method('handle')->willReturnCallback(function ($params, $next) {
            // Добавляем параметр для проверки
            $params['middleware'] = true;
            return $next($params);
        });

        $endpointMock = $this->createMock(EndpointInterface::class);
        $endpointMock->method('process')->willReturnCallback(function ($params) {
            $this->assertArrayHasKey('middleware', $params);
            $this->assertTrue($params['middleware']);
            return ['success' => true];
        });

        RestApiRouter::register('GET', '/test', $endpointMock, [$middlewareMock]);

        $requestMock = $this->createMock(Request::class);
        $requestMock->method('getMethod')->willReturn('GET');
        $requestMock->method('getPath')->willReturn('/test');
        $requestMock->method('getQueryParams')->willReturn([]);

        $response = RestApiRouter::handle($requestMock);

        $this->assertSame(['success' => true], $response);
    }

    public function testHandleRequestRouteNotFound(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->method('getMethod')->willReturn('GET');
        $requestMock->method('getPath')->willReturn('/not-found');

        $response = RestApiRouter::handle($requestMock);

        $this->assertEquals(['error' => 'Route not found'], $response);
        $this->assertEquals(404, http_response_code());
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

        // Пытаемся зарегистрировать маршрут с некорректным middleware
        RestApiRouter::register('GET', '/test', $endpointMock, ['InvalidMiddleware']);
    }
}
