<?php

namespace MiniCore\Tests\Http;

use PHPUnit\Framework\TestCase;
use MiniCore\Http\Request;

/**
 * Unit tests for the Request class.
 *
 * This test suite verifies the core functionality of the Request class,
 * ensuring that HTTP requests are properly constructed, normalized, and accessible.
 *
 * Covered functionality:
 * - Proper initialization of the Request object with method, path, query, body, and headers.
 * - Accessing query and body parameters via `getParam()` and `getParams()`.
 * - Handling and retrieving headers.
 * - Normalizing request paths.
 * - Creating a Request object from global server variables.
 */
class RequestTest extends TestCase
{
    /**
     * Tests the correct initialization of the Request object.
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
     * Tests retrieving query and body parameters using getParam().
     */
    public function testGetParam()
    {
        $request = new Request('GET', '/search', ['q' => 'php'], ['page' => 2]);

        $this->assertEquals('php', $request->getParam('q'));
        $this->assertEquals(2, $request->getParam('page'));
        $this->assertEquals('default', $request->getParam('none', 'default'));
    }

    /**
     * Tests merging query and body parameters using getParams().
     */
    public function testGetParams()
    {
        $request = new Request('POST', '/submit', ['id' => 1], ['name' => 'John']);
        $this->assertEquals(['id' => 1, 'name' => 'John'], $request->getParams());
    }

    /**
     * Tests header retrieval and case insensitivity.
     */
    public function testGetHeader()
    {
        $request = new Request('GET', '/info', [], [], ['authorization' => 'Bearer token']);

        $this->assertEquals('Bearer token', $request->getHeader('Authorization'));
        $this->assertNull($request->getHeader('Non-Existent'));
        $this->assertEquals('default', $request->getHeader('Non-Existent', 'default'));
    }

    /**
     * Tests path normalization to ensure consistent formatting.
     */
    public function testNormalizePath()
    {
        $reflection = new \ReflectionClass(Request::class);
        $method = $reflection->getMethod('normalizePath');
        $method->setAccessible(true);

        $request = new Request('GET', '/');

        $this->assertEquals('/api/user', $method->invokeArgs($request, ['/api/user/']));
        $this->assertEquals('/api/user', $method->invokeArgs($request, ['api/user']));
        $this->assertEquals('/api/user', $method->invokeArgs($request, ['//api//user//']));
    }

    /**
     * Tests creating a Request object from global server variables.
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
     * Cleans up superglobal variables after each test.
     */
    protected function tearDown(): void
    {
        $_SERVER = [];
        $_GET = [];
        $_POST = [];
    }
}
