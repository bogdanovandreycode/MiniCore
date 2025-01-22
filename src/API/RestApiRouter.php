<?php

namespace MiniCore\API;

use MiniCore\Http\Request;
use MiniCore\API\EndpointInterface;
use MiniCore\API\MiddlewareInterface;

/**
 * Class RestApiRouter
 *
 * Handles registration and processing of REST API routes, including middleware support.
 */
class RestApiRouter
{
    /**
     * Array to store registered REST API routes.
     *
     * Structure:
     * [
     *    'GET' => [
     *       '/api/users' => [
     *          'endpoint' => EndpointInterface,
     *          'middlewares' => [MiddlewareInterface, ...]
     *       ]
     *    ]
     * ]
     */
    private static array $routes = [];

    /**
     * Register a REST API route with its associated endpoint and middleware.
     *
     * @param string $method HTTP method (e.g., GET, POST).
     * @param string $path The route path (e.g., /api/users).
     * @param EndpointInterface $endpoint The endpoint that handles the route.
     * @param array $middlewares List of middleware instances.
     *
     * @return void
     */
    public static function register(string $method, string $path, EndpointInterface $endpoint, array $middlewares = []): void
    {
        $normalizedMethod = strtoupper($method);
        $normalizedPath = self::normalizePath($path);
        $middlewares = self::validateMiddleware($middlewares);

        self::$routes[$normalizedMethod][$normalizedPath] = [
            'endpoint' => $endpoint,
            'middlewares' => $middlewares,
        ];
    }

    /**
     * Handle the request and find a matching REST API route.
     *
     * @param Request $request The incoming HTTP request.
     * @return mixed The response from the matched endpoint.
     * @throws \Exception If no matching route is found or the method is not allowed.
     */
    public static function handle(Request $request): mixed
    {
        try {
            $method = $request->getMethod();
            $path = $request->getPath();

            if (!isset(self::$routes[$method])) {
                throw new \Exception('Method not allowed', 405);
            }

            if (!isset(self::$routes[$method][$path])) {
                throw new \Exception('Endpoint not found', 404);
            }

            $route = self::$routes[$method][$path];
            $endpoint = $route['endpoint'];
            $middlewares = $route['middlewares'];

            $handler = function (array $params) use ($endpoint) {
                return $endpoint->process($params);
            };

            foreach (array_reverse($middlewares) as $middleware) {
                $handler = function (array $params) use ($middleware, $handler) {
                    return $middleware->handle($params, $handler);
                };
            }

            return $handler($request->getQueryParams());
        } catch (\Exception $e) {
            http_response_code($e->getCode() ?: 500);
            return ['error' => $e->getMessage()];
        }
    }


    /**
     * Normalize the route path by ensuring it starts with a single '/' and has no trailing slashes.
     *
     * @param string $path The route path.
     * @return string The normalized path.
     */
    private static function normalizePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('#/+#', '/', $path);
        return '/' . trim($path, '/');
    }

    /**
     * Get all registered REST API routes.
     *
     * @return array The list of registered routes.
     */
    public static function getRoutes(): array
    {
        return self::$routes;
    }

    private static function validateMiddleware(array $middlewares): array
    {
        foreach ($middlewares as $middleware) {
            if (!$middleware instanceof MiddlewareInterface) {
                throw new \RuntimeException("All middleware must implement MiddlewareInterface.");
            }
        }

        return $middlewares;
    }
}
