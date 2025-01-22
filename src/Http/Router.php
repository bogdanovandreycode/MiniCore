<?php

namespace MiniCore\Http;

use MiniCore\Http\RouteInterface;

/**
 * Class Router
 *
 * Handles HTTP routing by registering routes and dispatching requests to the appropriate endpoint handlers.
 * Supports basic route registration for different HTTP methods and provides route normalization for consistent matching.
 *
 * @package MiniCore\Http
 *
 * @example
 * // Registering a GET route
 * Router::register('GET', '/api/user', new UserEndpoint());
 *
 * // Handling an incoming request
 * $request = Request::fromGlobals();
 * $response = Router::handle($request);
 */
class Router
{
    /**
     * Array to store registered routes.
     *
     * Structure: [
     *    'GET' => [
     *        '/api/user' => RouteInterface
     *    ],
     *    'POST' => [
     *        '/api/user' => RouteInterface
     *    ]
     * ]
     */
    private static array $routes = [];

    /**
     * Register a route with its associated endpoint.
     *
     * @param string $method HTTP method (e.g., GET, POST).
     * @param string $path The route path (e.g., /api/user).
     * @param RouteInterface $endpoint The endpoint that handles the route.
     *
     * @return void
     *
     * @example
     * Router::register('POST', '/api/user', new CreateUserEndpoint());
     */
    public static function register(string $method, string $path, RouteInterface $endpoint): void
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
     *
     * @example
     * $request = Request::fromGlobals();
     * $response = Router::handle($request);
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
     *
     * @example
     * $routes = Router::getRoutes();
     * print_r($routes);
     */
    public static function getRoutes(): array
    {
        return self::$routes;
    }

    /**
     * Normalize the route path by ensuring it starts with a single '/' and has no trailing slashes.
     *
     * @param string $path The route path.
     * @return string The normalized path.
     *
     * @example
     * $normalized = Router::normalizePath('/api/user/');
     * echo $normalized; // Output: /api/user
     */
    private static function normalizePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('#/+#', '/', $path);
        return '/' . trim($path, '/');
    }
}
