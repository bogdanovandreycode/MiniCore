<?php

namespace MiniCore;

use MiniCore\Config\Env;
use MiniCore\Http\Router;
use MiniCore\Http\Request;
use MiniCore\Http\Response;
use MiniCore\View\ViewLoader;
use MiniCore\Database\DataBase;
use MiniCore\Config\RouteLoader;
use MiniCore\Module\ModuleManager;
use MiniCore\Config\AdminConfigLoader;
use MiniCore\Database\DefaultTable\PostsTable;
use MiniCore\Database\DefaultTable\RolesTable;
use MiniCore\Database\DefaultTable\UsersTable;
use MiniCore\Database\DefaultTable\PostMetaTable;
use MiniCore\Database\DefaultTable\SettingsTable;
use MiniCore\Database\DefaultTable\UserMetaTable;
use MiniCore\Database\DefaultTable\UserRolesTable;


class Boot
{
    private static Request $request;
    private static string $rootDir;
    private static string $configDir;
    private static string $viewDir;
    private static string $moduleDir;

    /**
     * Main entry point for the library.
     *
     * @param string $rootDir Root directory of the project.
     * @param string $configDir Path to the configuration directory.
     * @param string $viewDir Path to the views directory.
     * @param string $moduleDir Path to the modules directory.
     * @return void
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
     * Start a session if not already started.
     *
     * @return void
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
     * Load environment variables.
     *
     * @return void
     */
    private static function loadEnvironment(): void
    {
        Env::load(self::$configDir, 'app.env');

        self::$request = Request::fromGlobals();

        ViewLoader::loadConfig(
            self::$configDir . '/views.yml',
            self::$viewDir,
        );

        AdminConfigLoader::load(self::$configDir . '/admin.yml');
        RouteLoader::load(self::$configDir . '/routes.yml');
    }

    /**
     * Initialize modules.
     *
     * @return void
     */
    private static function initializeModules(): void
    {
        // Load modules
        ModuleManager::loadModules(self::$moduleDir, self::$configDir . '/modules.yml');

        // Initialize modules
        ModuleManager::initializeModules();

        // Load module-specific routes and views
        RouteLoader::loadFromModules();
        ViewLoader::loadFromModules();
    }

    /**
     * Set up the database connection.
     *
     * @return void
     */
    private static function setupDatabase(): void
    {
        DataBase::setConnection(Env::get('DB_HOST'), Env::get('DB_NAME'), Env::get('DB_USER'), Env::get('DB_PASSWORD'));
        DataBase::addTable(new UsersTable());
        DataBase::addTable(new UserMetaTable());
        DataBase::addTable(new PostsTable());
        DataBase::addTable(new PostMetaTable());
        DataBase::addTable(new RolesTable());
        DataBase::addTable(new UserRolesTable());
        DataBase::addTable(new SettingsTable());
        DataBase::createTables();
    }

    /**
     * Set up error and exception handling.
     *
     * @return void
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
     * Handle the incoming HTTP request (optional, if library handles routing).
     *
     * @return void
     */
    public static function handleRequest(): void
    {
        try {
            $response = Router::handle(self::$request);
            (new Response(200, $response))->send();
        } catch (\Exception $e) {
            (new Response($e->getCode() ?: 500, ['error' => $e->getMessage()]))->send();
        }
    }
}
