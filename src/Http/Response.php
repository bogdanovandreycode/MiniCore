<?php

namespace MiniCore\Http;

/**
 * Class Response
 *
 * Handles HTTP responses by managing status codes, response bodies, and headers.
 * This class provides methods for setting headers, defining response bodies, and
 * sending the response to the client.
 *
 * @package MiniCore\Http
 *
 * @example
 * // Sending a simple plain text response
 * $response = new Response(200, 'Hello, World!');
 * $response->send();
 *
 * @example
 * // Sending a JSON response with custom headers
 * $response = new Response(201, ['message' => 'Resource created']);
 * $response->setHeader('Content-Type', 'application/json');
 * $response->send();
 */
class Response
{
    private int $statusCode;
    private mixed $body;
    private array $headers;

    /**
     * Response constructor.
     *
     * @param int $statusCode The HTTP status code (e.g., 200, 404).
     * @param mixed $body The body of the response (e.g., string, array, etc.).
     * @param array $headers The headers to include in the response.
     *
     * @example
     * // Creating a response with a 404 status code and custom message
     * $response = new Response(404, 'Page not found');
     */
    public function __construct(int $statusCode = 200, mixed $body = null, array $headers = [])
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->headers = $headers;
    }

    /**
     * Set an HTTP header.
     *
     * @param string $name The name of the header.
     * @param string $value The value of the header.
     * @return void
     *
     * @example
     * $response->setHeader('Content-Type', 'application/json');
     */
    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    /**
     * Get the response body.
     *
     * @return mixed The response body content.
     *
     * @example
     * $body = $response->getBody(); // Get the current response body
     */
    public function getBody(): mixed
    {
        return $this->body;
    }

    /**
     * Get the HTTP status code.
     *
     * @return int The HTTP status code.
     *
     * @example
     * $statusCode = $response->getStatusCode(); // 200
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Send the response to the client.
     *
     * Outputs the headers, status code, and body content.
     *
     * @return void
     *
     * @example
     * $response = new Response(200, ['status' => 'success']);
     * $response->send(); // Sends JSON response: {"status":"success"}
     */
    public function send(): void
    {
        // Send HTTP status code
        http_response_code($this->statusCode);

        // Send headers
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        // Send body
        if (is_array($this->body) || is_object($this->body)) {
            header('Content-Type: application/json');
            echo json_encode($this->body);
        } elseif ($this->body !== null) {
            echo $this->body;
        }
    }
}
