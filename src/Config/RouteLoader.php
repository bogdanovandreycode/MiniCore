<?php

namespace MiniCore\Config;

use MiniCore\Http\Router;
use Symfony\Component\Yaml\Yaml;
use MiniCore\Module\ModuleManager;
use MiniCore\API\EndpointInterface;

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
     * Load routes from all active modules.
     *
     * @return void
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
