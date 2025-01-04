<?php

namespace MiniCore\Http;

use MiniCore\API\EndpointInterface;

class Router
{
    private static array $routes = [];

    /**
     * Register a route with its associated endpoint.
     *
     * @param string $method HTTP method (e.g., GET, POST).
     * @param string $path The route path (e.g., /api/user).
     * @param EndpointInterface $endpoint The endpoint that handles the route.
     */
    public static function register(string $method, string $path, EndpointInterface $endpoint): void
    {
        $normalizedMethod = strtoupper($method);
        $normalizedPath = self::normalizePath($path);

        self::$routes[$normalizedMethod][$normalizedPath] = $endpoint;
    }

    /**
     * Handle the request and find a matching route.
     *
     * @param Request $request The incoming HTTP request.
     * @return mixed The response from the matched endpoint.
     * @throws \Exception If no matching route is found or the method is not allowed.
     */
    public static function handle(Request $request): mixed
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        if (!isset(self::$routes[$method])) {
            throw new \Exception('Method not allowed', 405);
        }

        if (!isset(self::$routes[$method][$path])) {
            throw new \Exception('Route not found', 404);
        }

        $endpoint = self::$routes[$method][$path];
        return $endpoint->handle($request->getQueryParams());
    }

    /**
     * Get all registered routes.
     *
     * @return array The list of registered routes.
     */
    public static function getRoutes(): array
    {
        return self::$routes;
    }

    /**
     * Normalize the route path.
     *
     * @param string $path The route path.
     * @return string The normalized path.
     */
    private static function normalizePath(string $path): string
    {
        return '/' . trim($path, '/');
    }
}
