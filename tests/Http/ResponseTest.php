<?php

namespace Tests\Http;

use PHPUnit\Framework\TestCase;
use MiniCore\Http\Response;

class ResponseTest extends TestCase
{
    /**
     * Проверка установки статус-кода
     */
    public function testStatusCode()
    {
        $response = new Response(404, 'Not Found');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Проверка установки и получения заголовков
     */
    public function testSetHeader()
    {
        $response = new Response();
        $response->setHeader('Content-Type', 'application/json');

        // Используем рефлексию для проверки приватного свойства headers
        $reflection = new \ReflectionClass($response);
        $property = $reflection->getProperty('headers');
        $property->setAccessible(true);
        $headers = $property->getValue($response);

        $this->assertArrayHasKey('Content-Type', $headers);
        $this->assertEquals('application/json', $headers['Content-Type']);
    }

    /**
     * Проверка тела ответа
     */
    public function testResponseBody()
    {
        $body = ['status' => 'success'];
        $response = new Response(200, $body);
        $this->assertEquals($body, $response->getBody());
    }

    /**
     * Проверка метода send() с текстовым ответом
     */
    public function testSendTextResponse()
    {
        $response = new Response(200, 'Hello, World!');

        ob_start(); // Включаем буферизацию вывода
        $response->send();
        $output = ob_get_clean(); // Получаем вывод

        $this->assertEquals('Hello, World!', $output);
    }

    /**
     * Проверка метода send() с JSON ответом
     */
    public function testSendJsonResponse()
    {
        $body = ['status' => 'success'];
        $response = new Response(200, $body);

        ob_start();
        $response->send();
        $output = ob_get_clean();

        $this->assertJson($output);
        $this->assertEquals(json_encode($body), $output);
    }
}
