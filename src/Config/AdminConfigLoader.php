<?php

namespace MiniCore\Config;

use Symfony\Component\Yaml\Yaml;

class AdminConfigLoader
{
    private static array $config = [];

    /**
     * Load the admin configuration from a YAML file.
     *
     * @param string $filePath Path to the admin.yml file.
     * @return void
     * @throws \RuntimeException If the file is not found or invalid.
     */
    public static function load(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("Admin configuration file not found: $filePath");
        }

        $data = Yaml::parseFile($filePath);

        if (!isset($data['admin'])) {
            throw new \RuntimeException("Invalid admin configuration file: missing 'admin' section.");
        }

        self::$config = $data['admin'];
    }

    /**
     * Get the full configuration array.
     *
     * @return array The admin configuration.
     */
    public static function getConfig(): array
    {
        return self::$config;
    }

    /**
     * Get a specific configuration value by key.
     *
     * @param string $key The key to retrieve.
     * @param mixed $default The default value if the key is not found.
     * @return mixed The configuration value.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return self::$config[$key] ?? $default;
    }
}
