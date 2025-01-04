<?php

namespace Vendor\Undermarket\Core\Config;

use Symfony\Component\Yaml\Yaml;
use Vendor\Undermarket\Core\Http\Router;
use Vendor\Undermarket\Core\API\EndpointInterface;

class RouteLoader
{
    /**
     * Load routes from the YAML file and register them in the router.
     *
     * @param string $configPath The path to the routes YAML file.
     * @return void
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
     * Register a single route in the router.
     *
     * @param array $route The route configuration.
     * @return void
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
