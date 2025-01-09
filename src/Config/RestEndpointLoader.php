<?php

namespace MiniCore\API;

use MiniCore\Http\Router;
use Symfony\Component\Yaml\Yaml;
use MiniCore\Module\ModuleManager;

class RestEndpointLoader
{
    /**
     * Load endpoints from a YAML file and register them.
     *
     * @param string $configPath The path to the rest_endpoints.yml file.
     * @return void
     */
    public static function load(string $configPath): void
    {
        if (!file_exists($configPath)) {
            throw new \RuntimeException("Endpoints file not found: $configPath");
        }

        $data = Yaml::parseFile($configPath)['endpoints'] ?? [];

        foreach ($data as $endpoint) {
            self::registerEndpoint($endpoint);
        }
    }

    /**
     * Load endpoints from all active modules.
     *
     * @return void
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
     * Register a single endpoint.
     *
     * @param array $endpoint The endpoint configuration.
     * @return void
     */
    private static function registerEndpoint(array $endpoint): void
    {
        if (!isset($endpoint['method'], $endpoint['route'], $endpoint['handler'])) {
            throw new \RuntimeException("Invalid endpoint configuration.");
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

        Router::register($method, $route, $handler);
    }
}
