<?php

namespace Vendor\Undermarket\Core\Http;

class Request
{
    private string $method;
    private string $path;
    private array $queryParams;
    private array $bodyParams;
    private array $headers;

    public function __construct(string $method, string $path, array $queryParams = [], array $bodyParams = [], array $headers = [])
    {
        $this->method = strtoupper($method);
        $this->path = $this->normalizePath($path);
        $this->queryParams = $queryParams;
        $this->bodyParams = $bodyParams;
        $this->headers = $headers ?: $this->extractHeaders();
    }

    /**
     * Create a Request instance from global server variables.
     */
    public static function fromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $queryParams = $_GET;
        $bodyParams = $_POST;
        $headers = self::extractHeaders();

        return new self($method, $path, $queryParams, $bodyParams, $headers);
    }

    /**
     * Get the HTTP method of the request.
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get the request path (normalized).
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get all query parameters.
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * Get all body parameters.
     */
    public function getBodyParams(): array
    {
        return $this->bodyParams;
    }

    /**
     * Get a single query or body parameter by key.
     */
    public function getParam(string $key, mixed $default = null): mixed
    {
        return $this->queryParams[$key] ?? $this->bodyParams[$key] ?? $default;
    }

    /**
     * Get all request headers.
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get a specific header by name.
     */
    public function getHeader(string $name, mixed $default = null): mixed
    {
        $normalized = strtolower($name);
        return $this->headers[$normalized] ?? $default;
    }

    /**
     * Normalize the request path.
     */
    private function normalizePath(string $path): string
    {
        return '/' . trim(parse_url($path, PHP_URL_PATH) ?? '/', '/');
    }

    /**
     * Extract headers from the global server variables.
     */
    private static function extractHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $normalizedKey = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$normalizedKey] = $value;
            }
        }
        return $headers;
    }

    public function getParams(): array
    {
        return array_merge($this->queryParams, $this->bodyParams);
    }
}
