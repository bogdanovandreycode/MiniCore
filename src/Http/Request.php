<?php

namespace MiniCore\Http;

/**
 * Class Request
 *
 * Encapsulates the HTTP request, providing access to method, path, query parameters,
 * body parameters, and headers. Designed to simplify request handling in web applications.
 *
 * @package MiniCore\Http
 *
 * @example
 * // Create a new Request manually
 * $request = new Request('POST', '/submit', ['id' => 42], ['name' => 'John']);
 * echo $request->getMethod(); // POST
 * echo $request->getPath();   // /submit
 */
class Request
{
    private string $method;
    private string $path;
    private array $queryParams;
    private array $bodyParams;
    private array $headers;

    /**
     * Request constructor.
     *
     * @param string $method      HTTP method (GET, POST, etc.).
     * @param string $path        Request path (URI).
     * @param array  $queryParams Query parameters ($_GET).
     * @param array  $bodyParams  Body parameters ($_POST).
     * @param array  $headers     Request headers.
     */
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
     *
     * @return self
     *
     * @example
     * // Automatically create a Request object from PHP superglobals
     * $request = Request::fromGlobals();
     * echo $request->getMethod(); // GET or POST
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
     *
     * @return string
     *
     * @example
     * $method = $request->getMethod(); // "GET" or "POST"
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get the request path (normalized).
     *
     * @return string
     *
     * @example
     * $path = $request->getPath(); // "/api/users"
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get all query parameters.
     *
     * @return array
     *
     * @example
     * $queryParams = $request->getQueryParams(); // ['id' => 1, 'page' => 2]
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * Get all body parameters.
     *
     * @return array
     *
     * @example
     * $bodyParams = $request->getBodyParams(); // ['username' => 'admin', 'password' => 'secret']
     */
    public function getBodyParams(): array
    {
        return $this->bodyParams;
    }

    /**
     * Get a single query or body parameter by key.
     *
     * @param string $key     The parameter key.
     * @param mixed  $default Default value if the key is not found.
     *
     * @return mixed
     *
     * @example
     * $id = $request->getParam('id', 0); // Returns 'id' or 0 if not set
     */
    public function getParam(string $key, mixed $default = null): mixed
    {
        return $this->queryParams[$key] ?? $this->bodyParams[$key] ?? $default;
    }

    /**
     * Get all request headers.
     *
     * @return array
     *
     * @example
     * $headers = $request->getHeaders(); // ['content-type' => 'application/json']
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get a specific header by name.
     *
     * @param string $name    The header name.
     * @param mixed  $default Default value if the header is not set.
     *
     * @return mixed
     *
     * @example
     * $authToken = $request->getHeader('Authorization'); // "Bearer token"
     */
    public function getHeader(string $name, mixed $default = null): mixed
    {
        $normalized = strtolower($name);
        return $this->headers[$normalized] ?? $default;
    }

    /**
     * Normalize the request path.
     *
     * @param string $path
     * @return string
     */
    private function normalizePath(string $path): string
    {
        return '/' . trim(parse_url($path, PHP_URL_PATH) ?? '/', '/');
    }

    /**
     * Extract headers from the global server variables.
     *
     * @return array
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

    /**
     * Get all combined query and body parameters.
     *
     * @return array
     *
     * @example
     * $params = $request->getParams(); // Merges $_GET and $_POST
     */
    public function getParams(): array
    {
        return array_merge($this->queryParams, $this->bodyParams);
    }
}
