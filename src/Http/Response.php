<?php

namespace MiniCore\Http;

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
     */
    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    /**
     * Get the response body.
     */
    public function getBody(): mixed
    {
        return $this->body;
    }

    /**
     * Get the HTTP status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Send the response to the client.
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
