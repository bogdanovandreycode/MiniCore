<?php

namespace MiniCore\Config;

use MiniCore\Http\Router;
use Symfony\Component\Yaml\Yaml;
use MiniCore\Module\ModuleManager;
use MiniCore\API\EndpointInterface;

/**
 * Class RouteLoader
 *
 * Responsible for loading and registering HTTP routes from YAML configuration files.
 * Supports loading routes from both the core application and all active modules.
 * 
 * Each route is registered in the application's Router, allowing proper request handling.
 *
 * @example
 * // Load routes from the core application configuration:
 * RouteLoader::load(__DIR__ . '/Config/routes.yml');
 *
 * // Load routes from all active modules:
 * RouteLoader::loadFromModules();
 */
class RouteLoader
{
    /**
     * Load routes from a YAML configuration file and register them in the router.
     *
     * This method reads the specified YAML file and registers each route
     * by mapping HTTP methods, paths, and handlers.
     *
     * @param string $configPath The path to the `routes.yml` file.
     * @return void
     *
     * @throws \RuntimeException If the configuration file does not exist or is invalid.
     *
     * @example
     * // Load routes from a specific file:
     * RouteLoader::load(__DIR__ . '/Config/routes.yml');
     */
    public static function load(string $configPath): void
    {
        if (!file_exists($configPath)) {
            throw new \RuntimeException("Routes file not found: $configPath");
        }

        $routes = Yaml::parseFile($configPath);

        foreach ($routes as $route) {
            self::registerRoute($route);
        }
    }

    /**
     * Load and register routes from all active modules.
     *
     * This method scans all enabled modules for the `routes.yml` file
     * and registers their routes, enabling modular route configuration.
     *
     * @return void
     *
     * @example
     * // Automatically load routes from all modules:
     * RouteLoader::loadFromModules();
     */
    public static function loadFromModules(): void
    {
        foreach (ModuleManager::getModules() as $module) {
            $routeConfig = $module->getPath() . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'routes.yml';

            if (file_exists($routeConfig)) {
                $routes = Yaml::parseFile($routeConfig);

                foreach ($routes as $route) {
                    self::registerRoute($route);
                }
            }
        }
    }

    /**
     * Register a single route in the router.
     *
     * Validates the route configuration and registers it in the application's router.
     *
     * @param array $route The route configuration containing `method`, `path`, and `handler`.
     * @return void
     *
     * @throws \RuntimeException If the configuration is invalid or the handler is missing.
     *
     * @example
     * // Example YAML route configuration:
     * // - method: GET
     * //   path: "/users"
     * //   handler: "Modules\\UserModule\\Controllers\\UserController"
     */
    private static function registerRoute(array $route): void
    {
        if (!isset($route['method'], $route['path'], $route['handler'])) {
            throw new \RuntimeException("Invalid route configuration.");
        }

        $method = strtoupper($route['method']);
        $path = $route['path'];
        $handlerClass = $route['handler'];

        if (!class_exists($handlerClass)) {
            throw new \RuntimeException("Handler class not found: $handlerClass");
        }

        $handler = new $handlerClass();

        if (!$handler instanceof EndpointInterface) {
            throw new \RuntimeException("Handler must implement EndpointInterface: $handlerClass");
        }

        Router::register($method, $path, $handler);
    }
}
