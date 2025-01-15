<?php

namespace Tests\Http;

use PHPUnit\Framework\TestCase;
use MiniCore\Http\Router;
use MiniCore\Http\Request;
use MiniCore\API\EndpointInterface;

class RouterTest extends TestCase
{
    protected function setUp(): void
    {
        // Очищаем маршруты перед каждым тестом
        $reflection = new \ReflectionClass(Router::class);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        $routesProperty->setValue([]);
    }

    /**
     * Тест регистрации маршрута
     */
    public function testRegisterRoute()
    {
        $endpointMock = $this->createMock(EndpointInterface::class);
        Router::register('GET', '/test', $endpointMock);

        $routes = Router::getRoutes();

        $this->assertArrayHasKey('GET', $routes);
        $this->assertArrayHasKey('/test', $routes['GET']);
        $this->assertSame($endpointMock, $routes['GET']['/test']);
    }

    /**
     * Тест обработки запроса с существующим маршрутом
     */
    public function testHandleRequestWithRegisteredRoute()
    {
        $endpointMock = $this->createMock(EndpointInterface::class);
        $endpointMock->expects($this->once())
            ->method('handle')
            ->with(['param' => 'value'])
            ->willReturn('Handled Response');

        Router::register('GET', '/test', $endpointMock);

        $requestMock = $this->createMock(Request::class);
        $requestMock->method('getMethod')->willReturn('GET');
        $requestMock->method('getPath')->willReturn('/test');
        $requestMock->method('getQueryParams')->willReturn(['param' => 'value']);

        $response = Router::handle($requestMock);

        $this->assertEquals('Handled Response', $response);
    }

    /**
     * Тест обработки запроса с несуществующим маршрутом
     */
    public function testHandleRequestWithUnregisteredRoute()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Route not found');
        $this->expectExceptionCode(404);

        // Регистрируем маршрут с методом GET, но другим путем
        $endpointMock = $this->createMock(EndpointInterface::class);
        Router::register('GET', '/existing-route', $endpointMock);

        // Проверяем запрос на несуществующий маршрут
        $requestMock = $this->createMock(Request::class);
        $requestMock->method('getMethod')->willReturn('GET');
        $requestMock->method('getPath')->willReturn('/not-registered');

        Router::handle($requestMock);
    }

    /**
     * Тест обработки запроса с неразрешённым методом
     */
    public function testHandleRequestWithUnsupportedMethod()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Method not allowed');
        $this->expectExceptionCode(405);

        $requestMock = $this->createMock(Request::class);
        $requestMock->method('getMethod')->willReturn('DELETE');
        $requestMock->method('getPath')->willReturn('/test');

        Router::handle($requestMock);
    }

    /**
     * Тест нормализации маршрута
     */
    public function testRouteNormalization()
    {
        $reflection = new \ReflectionClass(Router::class);
        $method = $reflection->getMethod('normalizePath');
        $method->setAccessible(true);

        $this->assertEquals('/api/user', $method->invoke(null, '/api/user/'));
        $this->assertEquals('/api/user', $method->invoke(null, 'api/user'));
        $this->assertEquals('/api/user', $method->invoke(null, '\\api\\user'));
        $this->assertEquals('/api/user', $method->invoke(null, '\\api\\user\\'));
        $this->assertEquals('/api/user', $method->invoke(null, '//api//user//'));
    }
}
