<?php

namespace Vendor\Undermarket\Core\Config;

use Dotenv\Dotenv;

class Env
{
    /**
     * Load environment variables from a .env file.
     *
     * @param string $envPath The path to the directory containing the .env file.
     * @param string $envFile The name of the .env file (default: `.env`).
     * @return void
     */
    public static function load(string $envPath, string $envFile = '.env'): void
    {
        if (!file_exists($envPath . '/' . $envFile)) {
            throw new \RuntimeException("Environment file not found: $envPath/$envFile");
        }

        $envPath = realpath($envPath);

        $dotenv = Dotenv::createImmutable($envPath, $envFile);
        $dotenv->load();
    }

    /**
     * Get an environment variable value with an optional default.
     *
     * @param string $key The name of the environment variable.
     * @param mixed $default The default value to return if the variable is not set.
     * @return mixed The value of the environment variable or the default value.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $default;
    }

    /**
     * Set or override an environment variable.
     *
     * @param string $key The name of the environment variable.
     * @param mixed $value The value to set.
     * @return void
     */
    public static function set(string $key, mixed $value): void
    {
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}
