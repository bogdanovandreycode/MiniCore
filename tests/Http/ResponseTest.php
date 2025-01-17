<?php

namespace MiniCore\Tests\Http;

use PHPUnit\Framework\TestCase;
use MiniCore\Http\Response;

/**
 * Unit tests for the Response class.
 *
 * This test suite verifies the correct functionality of the Response class,
 * ensuring that HTTP responses are properly constructed and sent.
 *
 * Covered functionality:
 * - Setting and retrieving HTTP status codes.
 * - Managing response headers.
 * - Handling response body content.
 * - Sending plain text and JSON responses.
 */
class ResponseTest extends TestCase
{
    /**
     * Tests setting and retrieving the HTTP status code.
     */
    public function testStatusCode()
    {
        $response = new Response(404, 'Not Found');
        $this->assertEquals(404, $response->getStatusCode(), 'Status code should be 404.');
    }

    /**
     * Tests setting and retrieving headers.
     */
    public function testSetHeader()
    {
        $response = new Response();
        $response->setHeader('Content-Type', 'application/json');

        // Use reflection to verify the private headers property
        $reflection = new \ReflectionClass($response);
        $property = $reflection->getProperty('headers');
        $property->setAccessible(true);
        $headers = $property->getValue($response);

        $this->assertArrayHasKey('Content-Type', $headers, 'Header Content-Type should be set.');
        $this->assertEquals('application/json', $headers['Content-Type'], 'Header value should be application/json.');
    }

    /**
     * Tests setting and retrieving the response body.
     */
    public function testResponseBody()
    {
        $body = ['status' => 'success'];
        $response = new Response(200, $body);
        $this->assertEquals($body, $response->getBody(), 'Response body should match the provided data.');
    }

    /**
     * Tests sending a plain text response.
     */
    public function testSendTextResponse()
    {
        $response = new Response(200, 'Hello, World!');

        ob_start();
        $response->send();
        $output = ob_get_clean();

        $this->assertEquals('Hello, World!', $output, 'Response output should match the plain text content.');
    }

    /**
     * Tests sending a JSON response.
     */
    public function testSendJsonResponse()
    {
        $body = ['status' => 'success'];
        $response = new Response(200, $body);

        ob_start();
        $response->send();
        $output = ob_get_clean();

        $this->assertJson($output, 'Response output should be valid JSON.');
        $this->assertEquals(json_encode($body), $output, 'JSON output should match the response body.');
    }
}
