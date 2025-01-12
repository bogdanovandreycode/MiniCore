<?php

namespace MiniCore\Config;

use Dotenv\Dotenv;

/**
 * Class Env
 *
 * Manages loading and accessing environment variables from a `.env` file.
 * Provides methods to load environment configurations, retrieve variables,
 * and dynamically set or override environment values during runtime.
 *
 * This class simplifies configuration management by externalizing sensitive
 * or configurable data such as database credentials, API keys, and app settings.
 *
 * @example
 * // Example of loading and accessing environment variables:
 * Env::load(__DIR__ . '/../', '.env');
 *
 * $dbHost = Env::get('DB_HOST', 'localhost');
 * $appDebug = Env::get('APP_DEBUG', false);
 *
 * // Dynamically set a new environment variable
 * Env::set('NEW_VAR', 'value');
 */
class Env
{
    /**
     * Load environment variables from a .env file.
     *
     * Uses the `vlucas/phpdotenv` package to load environment variables
     * from a specified file into the global `$_ENV` and `putenv`.
     *
     * @param string $envPath The path to the directory containing the `.env` file.
     * @param string $envFile The name of the `.env` file (default: `.env`).
     * @return void
     *
     * @throws \RuntimeException If the `.env` file does not exist.
     *
     * @example
     * // Load the default `.env` file from the project root
     * Env::load(__DIR__ . '/../');
     *
     * // Load a custom `.env.production` file
     * Env::load(__DIR__ . '/../', '.env.production');
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
     * Retrieves the value of a given environment variable. If the variable
     * is not set, it returns the provided default value.
     *
     * @param string $key The name of the environment variable.
     * @param mixed $default The default value to return if the variable is not set.
     * @return mixed The value of the environment variable or the default value.
     *
     * @example
     * $dbHost = Env::get('DB_HOST', 'localhost');
     * $appMode = Env::get('APP_MODE', 'production');
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $default;
    }

    /**
     * Set or override an environment variable.
     *
     * Dynamically sets or updates the value of an environment variable.
     * This can be useful for modifying configurations at runtime.
     *
     * @param string $key The name of the environment variable.
     * @param mixed $value The value to set.
     * @return void
     *
     * @example
     * // Set or override a variable during execution
     * Env::set('CACHE_ENABLED', true);
     *
     * // Change database connection dynamically
     * Env::set('DB_HOST', '127.0.0.1');
     */
    public static function set(string $key, mixed $value): void
    {
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}
