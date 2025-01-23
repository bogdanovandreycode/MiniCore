<?php

namespace MiniCore\Config;

use MiniCore\API\RestApiRouter;
use Symfony\Component\Yaml\Yaml;
use MiniCore\Module\ModuleManager;
use MiniCore\API\EndpointInterface;

/**
 * Class RestEndpointLoader
 *
 * Responsible for loading and registering REST API endpoints from YAML configuration files.
 * Supports loading endpoints from both the core application and active modules.
 *
 * Endpoints are registered in the router, allowing them to handle HTTP requests.
 *
 * @example
 * // Example of loading API endpoints from a YAML file:
 * RestEndpointLoader::load(__DIR__ . '/Config/endpoints.yml');
 *
 * // Load endpoints from all active modules
 * RestEndpointLoader::loadFromModules();
 */
class RestEndpointLoader
{
    /**
     * Load endpoints from a YAML file and register them in the router.
     *
     * Parses the provided YAML file and registers each endpoint defined in it.
     *
     * @param string $configPath The path to the `endpoints.yml` file.
     * @return void
     *
     * @throws \RuntimeException If the file does not exist or is invalid.
     *
     * @example
     * // Load API endpoints from a configuration file
     * RestEndpointLoader::load(__DIR__ . '/Config/endpoints.yml');
     */
    public static function load(string $configPath): void
    {
        if (!file_exists($configPath)) {
            throw new \RuntimeException("Endpoints file not found: $configPath");
        }

        $data = Yaml::parseFile($configPath);

        if (empty($data) || !isset($data['endpoints']) || !is_array($data['endpoints'])) {
            return;
        }

        // Extract global middlewares if defined
        $globalMiddlewares = [];

        if (!empty($data['middlewares'])) {
            foreach ($data['middlewares'] as $middlewareClass) {
                if (!class_exists($middlewareClass)) {
                    throw new \RuntimeException("Middleware class not found: $middlewareClass");
                }

                $globalMiddlewares[] = new $middlewareClass();
            }
        }

        // Load endpoints from configuration
        foreach ($data['endpoints'] ?? [] as $endpoint) {
            self::registerEndpoint($endpoint, $globalMiddlewares);
        }
    }

    /**
     * Load endpoints from all active modules.
     *
     * Iterates through all enabled modules and loads their `endpoints.yml` files
     * if they exist. This allows modules to define their own REST API routes.
     *
     * @return void
     *
     * @example
     * // Automatically load API endpoints from all active modules
     * RestEndpointLoader::loadFromModules();
     */
    public static function loadFromModules(): void
    {
        foreach (ModuleManager::getModules() as $module) {
            $configPath = $module->getPath() . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'endpoints.yml';

            if (file_exists($configPath)) {
                self::load($configPath);
            }
        }
    }

    /**
     * Register a single endpoint in the router.
     *
     * Validates and registers the endpoint handler in the router
     * so that it can process HTTP requests.
     *
     * @param array $endpoint The endpoint configuration (method, route, handler).
     * @param array $globalMiddlewares List of global middleware instances.
     * @return void
     *
     * @throws \RuntimeException If the endpoint configuration is invalid or the handler is missing.
     *
     * @example
     * // Example endpoint configuration in YAML:
     * // - method: GET
     * //   route: "/api/users"
     * //   handler: "App\\Endpoints\\GetUsersEndpoint"
     * //   middlewares:
     * //     - "App\\Middleware\\AuthMiddleware"
     */
    private static function registerEndpoint(array $endpoint, array $globalMiddlewares = []): void
    {
        if (!isset($endpoint['method'], $endpoint['route'], $endpoint['handler'])) {
            throw new \RuntimeException("Invalid endpoint configuration.");
        }

        if (empty($endpoint['method']) || empty($endpoint['route']) || empty($endpoint['handler'])) {
            throw new \RuntimeException("Endpoint configuration contains empty values.");
        }

        $method = strtoupper($endpoint['method']);
        $route = $endpoint['route'];
        $handlerClass = $endpoint['handler'];

        if (!class_exists($handlerClass)) {
            throw new \RuntimeException("Handler class not found: $handlerClass");
        }

        $handler = new $handlerClass();

        if (!$handler instanceof EndpointInterface) {
            throw new \RuntimeException("Handler must implement EndpointInterface: $handlerClass");
        }

        // Extract route-specific middleware
        $routeMiddlewares = self::extractRouteMiddleware($endpoint['middlewares']);

        // Combine global and route-specific middleware
        $middlewares = array_merge($globalMiddlewares, $routeMiddlewares);

        // Register the endpoint with RestApiRouter
        RestApiRouter::register($method, $route, $handler, $middlewares);
    }

    /**
     * Extract route-specific middleware
     *
     * @param array $middlewares List of middleware class names.
     * @return array List of middleware instances for the route.
     *
     * @throws \RuntimeException If the endpoint configuration is invalid or the handler is missing.
     */
    private static function extractRouteMiddleware(?array $middlewares): array
    {
        $routeMiddlewares = [];

        if (empty($middlewares)) {
            return [];
        }

        foreach ($middlewares as $middlewareClass) {
            if (!class_exists($middlewareClass)) {
                throw new \RuntimeException("Middleware class not found: $middlewareClass");
            }

            $routeMiddlewares[] = new $middlewareClass();
        }

        return $routeMiddlewares;
    }
}
