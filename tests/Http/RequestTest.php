<?php

use PHPUnit\Framework\TestCase;
use MiniCore\Http\Request;

class RequestTest extends TestCase
{
    /**
     * Тест инициализации объекта Request
     */
    public function testRequestInitialization()
    {
        $request = new Request(
            'POST',
            '/submit',
            ['id' => 42],
            ['name' => 'John'],
            ['content-type' => 'application/json']
        );

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/submit', $request->getPath());
        $this->assertEquals(['id' => 42], $request->getQueryParams());
        $this->assertEquals(['name' => 'John'], $request->getBodyParams());
        $this->assertEquals('application/json', $request->getHeader('Content-Type'));
    }

    /**
     * Тест получения query и body параметров через getParam()
     */
    public function testGetParam()
    {
        $request = new Request('GET', '/search', ['q' => 'php'], ['page' => 2]);

        $this->assertEquals('php', $request->getParam('q'));         // Из query
        $this->assertEquals(2, $request->getParam('page'));          // Из body
        $this->assertEquals('default', $request->getParam('none', 'default')); // Отсутствующий параметр
    }

    /**
     * Тест объединения параметров через getParams()
     */
    public function testGetParams()
    {
        $request = new Request('POST', '/submit', ['id' => 1], ['name' => 'John']);
        $this->assertEquals(['id' => 1, 'name' => 'John'], $request->getParams());
    }

    /**
     * Тест работы с заголовками
     */
    public function testGetHeader()
    {
        $request = new Request(
            'GET',
            '/info',
            [],
            [],
            ['authorization' => 'Bearer token']
        );

        $this->assertEquals('Bearer token', $request->getHeader('Authorization'));
        $this->assertNull($request->getHeader('Non-Existent'));
        $this->assertEquals('default', $request->getHeader('Non-Existent', 'default'));
    }

    /**
     * Тест нормализации пути
     */
    public function testNormalizePath()
    {
        $reflection = new \ReflectionClass(Request::class);
        $method = $reflection->getMethod('normalizePath');
        $method->setAccessible(true);

        // Создаём объект Request
        $request = new Request('GET', '/');

        $this->assertEquals('/api/user', $method->invokeArgs($request, ['/api/user/']));
        $this->assertEquals('/api/user', $method->invokeArgs($request, ['api/user']));
        $this->assertEquals('/api/user', $method->invokeArgs($request, ['//api//user//']));
    }

    /**
     * Тест создания объекта из суперглобальных переменных
     */
    public function testFromGlobals()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/submit';
        $_GET = ['id' => 5];
        $_POST = ['name' => 'Jane'];
        $_SERVER['HTTP_CONTENT_TYPE'] = 'application/json';

        $request = Request::fromGlobals();

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/submit', $request->getPath());
        $this->assertEquals(['id' => 5], $request->getQueryParams());
        $this->assertEquals(['name' => 'Jane'], $request->getBodyParams());
        $this->assertEquals('application/json', $request->getHeader('Content-Type'));
    }

    /**
     * Очистка суперглобальных переменных после тестов
     */
    protected function tearDown(): void
    {
        $_SERVER = [];
        $_GET = [];
        $_POST = [];
    }
}
