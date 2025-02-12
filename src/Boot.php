<?php

namespace MiniCore;

use MiniCore\Config\Env;
use MiniCore\Http\Router;
use MiniCore\Http\Request;
use MiniCore\Http\Response;
use MiniCore\View\ViewLoader;
use MiniCore\API\RestApiRouter;
use MiniCore\Config\RouteLoader;
use MiniCore\Module\ModuleManager;
use MiniCore\Config\RestEndpointLoader;
use MiniCore\Database\DefaultTable\MySql\PostsTable;
use MiniCore\Database\DefaultTable\MySql\RolesTable;
use MiniCore\Database\DefaultTable\MySql\UsersTable;
use MiniCore\Database\DefaultRepository\MySqlDatabase;
use MiniCore\Database\DefaultTable\MySql\PostMetaTable;
use MiniCore\Database\DefaultTable\MySql\SettingsTable;
use MiniCore\Database\DefaultTable\MySql\UserMetaTable;
use MiniCore\Database\DefaultTable\MySql\UserRolesTable;

/**
 * Class Boot
 *
 * Central entry point for initializing and running the MiniCore framework.
 * This class is responsible for setting up the environment, database connections, loading modules, 
 * initializing views, managing routing, and handling HTTP requests and responses.
 *
 * @package MiniCore
 *
 * @example
 * // Example of running the framework
 * Boot::run(
 *     __DIR__,               // Project root directory
 *     __DIR__ . '/config',   // Config directory
 *     __DIR__ . '/views',    // Views directory
 *     __DIR__ . '/modules'   // Modules directory
 * );
 */
class Boot
{
    private static Request $request;
    private static string $rootDir;
    private static string $configDir;
    private static string $viewDir;
    private static string $moduleDir;

    /**
     * Initializes and runs the application.
     *
     * @param string $rootDir Root directory of the project.
     * @param string $configDir Path to the configuration directory.
     * @param string $viewDir Path to the views directory.
     * @param string $moduleDir Path to the modules directory.
     * @return void
     *
     * @example
     * Boot::run('/var/www/project', '/var/www/project/config', '/var/www/project/views', '/var/www/project/modules');
     */
    public static function run(string $rootDir, string $configDir, string $viewDir, string $moduleDir): void
    {
        self::$rootDir = $rootDir;
        self::$configDir = $configDir;
        self::$viewDir = $viewDir;
        self::$moduleDir = $moduleDir;

        self::loadEnvironment();
        self::setupErrorHandling();
        self::startSession();
        self::setupDatabase();
        self::initializeModules();
        self::handleRequest();
    }

    /**
     * Starts a session if it is not already started.
     *
     * @return void
     *
     * @example
     * Boot::startSession();
     */
    private static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_lifetime' => 3600,
                'cookie_secure' => isset($_SERVER['HTTPS']),
                'cookie_httponly' => true,
                'cookie_samesite' => 'Strict',
            ]);
        }
    }

    /**
     * Loads environment variables and configurations.
     *
     * @return void
     *
     * @example
     * Boot::loadEnvironment();
     */
    private static function loadEnvironment(): void
    {
        Env::load(self::$configDir, 'app.env');

        self::$request = Request::fromGlobals();

        ViewLoader::loadConfig(
            self::$configDir . '/views.yml',
            self::$viewDir,
        );

        RouteLoader::load(self::$configDir . '/routes.yml');
        RestEndpointLoader::load(self::$configDir . '/endpoints.yml');
    }

    /**
     * Initializes all active modules.
     *
     * @return void
     *
     * @example
     * Boot::initializeModules();
     */
    private static function initializeModules(): void
    {
        ModuleManager::loadModules(self::$configDir . '/modules.yml', self::$moduleDir);
        ModuleManager::initializeModules();

        RouteLoader::loadFromModules();
        RestEndpointLoader::loadFromModules();
        ViewLoader::loadFromModules();
    }

    /**
     * Sets up the database connection and initializes default tables.
     *
     * @return void
     *
     * @example
     * Boot::setupDatabase();
     */
    private static function setupDatabase(): void
    {
        new MySqlDatabase(
            [
                'host' => Env::get('DB_HOST'),
                'dbname' => Env::get('DB_NAME'),
                'user' => Env::get('DB_USER'),
                'password' => Env::get('DB_PASSWORD')
            ],
            [
                new UsersTable(),
                new UserMetaTable(),
                new PostsTable(),
                new PostMetaTable(),
                new RolesTable(),
                new UserRolesTable(),
                new SettingsTable(),
            ]
        );
    }

    /**
     * Configures error reporting and exception handling.
     *
     * @return void
     *
     * @example
     * Boot::setupErrorHandling();
     */
    private static function setupErrorHandling(): void
    {
        error_reporting(E_ALL);

        if (Env::get('APP_DEBUG', false)) {
            ini_set('display_errors', '1');
        } else {
            ini_set('display_errors', '0');
            ini_set('log_errors', '1');
            ini_set('error_log', self::$rootDir . '/../storage/logs/error.log');
        }

        set_exception_handler(function ($e) {
            http_response_code(500);
            echo 'An error occurred: ' . htmlspecialchars($e->getMessage());
            error_log($e);
        });

        set_error_handler(function ($severity, $message, $file, $line) {
            throw new \ErrorException($message, 0, $severity, $file, $line);
        });

        register_shutdown_function(function () {
            $error = error_get_last();
            if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
                http_response_code(500);
                echo 'A fatal error occurred. Please check the logs for more details.';
                error_log(json_encode($error));
            }
        });
    }

    /**
     * Handles the incoming HTTP request and generates a response.
     *
     * @return void
     *
     * @example
     * Boot::handleRequest();
     */
    public static function handleRequest(): void
    {
        try {
            $responseApi = RestApiRouter::handle(self::$request);

            if (!isset($responseApi['error'])) {
                (new Response(200, $responseApi))->send();
                return;
            }

            $responseRoute = Router::handle(self::$request);

            if (!isset($responseRoute['error'])) {
                (new Response(200, $responseRoute))->send();
                return;
            }

            (new Response(404, ['error' => 'Route or endpoint not found']))->send();
        } catch (\Exception $e) {
            (new Response($e->getCode() ?: 500, ['error' => $e->getMessage()]))->send();
            return;
        }
    }
}
