<?php

namespace MiniCore\Module;

use Symfony\Component\Yaml\Yaml;
use MiniCore\Module\AbstractModule;

/**
 * Class ModuleManager
 *
 * Central manager for handling modules within the MiniCore framework.  
 * Responsible for loading, initializing, and accessing modules based on configuration files.
 * Modules are dynamically discovered and instantiated according to PSR-4 autoloading rules.
 *
 * @package MiniCore\Module
 *
 * @example Loading and Initializing Modules:
 * try {
 *     // Load modules from a YAML config and initialize them
 *     ModuleManager::loadModules(__DIR__ . '/config/modules.yml');
 *     ModuleManager::initializeModules();
 *
 *     // Access a specific module
 *     $userModule = ModuleManager::getModule('UserModule');
 *     if ($userModule) {
 *         echo $userModule->getName(); // Example output: User Module
 *     }
 * } catch (\Exception $e) {
 *     echo 'Error: ' . $e->getMessage();
 * }
 */
class ModuleManager
{
    /**
     * Array of all loaded modules.
     *
     * @var AbstractModule[]
     */
    private static array $modules = [];

    /**
     * Array tracking initialized modules.
     *
     * @var bool[]
     */
    private static array $loadedModules = [];

    /**
     * Loads and initializes modules based on the provided YAML configuration file.
     *
     * This method reads the YAML configuration, locates and loads the corresponding `Module.php` files
     * for all enabled modules, and registers them for further use in the system.
     *
     * Only modules marked as `enabled: true` in the configuration will be loaded.
     * The module class must follow the namespace pattern: `Modules\{ModuleName}\Module`.
     *
     * @param string $configPath Path to the YAML configuration file (e.g., `modules.yml`).
     * @param string $modulesDir Path to the directory where module folders are located.
     *
     * @throws \Exception If the configuration file or module class file is missing.
     *
     * @example
     * // Example of loading modules from a YAML file and initializing them:
     * ModuleManager::loadModules(__DIR__ . '/config/modules.yml', __DIR__ . '/Modules');
     * ModuleManager::initializeModules();
     *
     * // Example of accessing a loaded module:
     * $userModule = ModuleManager::getModule('UserModule');
     * if ($userModule) {
     *     echo $userModule->getName(); // Output: User Module
     * }
     */
    public static function loadModules(string $configPath, string $modulesDir): void
    {
        if (!file_exists($configPath)) {
            throw new \Exception("Modules configuration file not found: $configPath");
        }

        $config = Yaml::parseFile($configPath)['modules'] ?? [];

        // Load only enabled modules
        self::loadModuleFiles($config, $modulesDir);

        foreach ($config as $moduleId => $moduleConfig) {
            if (!isset($moduleConfig['enabled']) || !$moduleConfig['enabled']) {
                continue; // Skip disabled modules
            }

            $className = self::findModuleClass($moduleId);

            if (!$className || !class_exists($className)) {
                throw new \Exception("Module class for '$moduleId' not found. Ensure proper PSR-4 autoloading.");
            }

            /** @var AbstractModule $module */
            $module = new $className();
            self::$modules[$moduleId] = $module;
        }
    }

    /**
     * Loads `Module.php` files only for enabled modules from the configuration.
     *
     * For each enabled module, the method looks for the file at the path: `{modulesDir}/{ModuleName}/Module.php`.
     * If the file is found, it is included using `require_once`, making the module class available for instantiation.
     *
     * @param array $modulesConfig Array of modules from the YAML configuration file.
     * @param string $modulesDir Directory where the modules are stored.
     *
     * @throws \Exception If the module file is missing.
     *
     * @example
     * // Assuming 'UserModule' is enabled in the YAML config, this will load:
     * // /Modules/UserModule/Module.php
     * self::loadModuleFiles($modulesConfig, __DIR__ . '/Modules');
     */
    private static function loadModuleFiles(array $modulesConfig, string $modulesDir): void
    {
        foreach ($modulesConfig as $moduleId => $moduleConfig) {
            if (!isset($moduleConfig['enabled']) || !$moduleConfig['enabled']) {
                continue; // Skip disabled modules
            }

            $moduleFile = rtrim($modulesDir, '/') . "/{$moduleId}/Module.php";

            if (file_exists($moduleFile)) {
                require_once $moduleFile;  // Makes the module class available
            } else {
                throw new \Exception("Module file not found for '$moduleId' at $moduleFile");
            }
        }
    }

    /**
     * Dynamically searches for a module class in the declared classes.
     *
     * Looks for a class matching the pattern 'Modules\{ModuleName}\Module'.
     *
     * @param string $moduleId Module identifier from the configuration.
     * @return string|null Fully qualified class name or null if not found.
     *
     * @example
     * $className = ModuleManager::findModuleClass('UserModule');
     * echo $className; // Output: MiniCore\Modules\UserModule\Module
     */
    private static function findModuleClass(string $moduleId): ?string
    {
        foreach (get_declared_classes() as $className) {
            if (preg_match('/Modules\\\\' . preg_quote($moduleId, '/') . '\\\\Module$/', $className)) {
                return $className;
            }
        }

        return null;
    }

    /**
     * Returns all registered modules.
     *
     * @return AbstractModule[] List of all loaded modules.
     *
     * @example
     * $modules = ModuleManager::getModules();
     * foreach ($modules as $id => $module) {
     *     echo "$id: " . $module->getName() . PHP_EOL;
     * }
     */
    public static function getModules(): array
    {
        return self::$modules;
    }

    /**
     * Returns all initialized modules.
     *
     * @return bool[] Array where keys are module IDs and values indicate initialization status.
     *
     * @example
     * $initialized = ModuleManager::getLoadedModules();
     * foreach ($initialized as $id => $status) {
     *     echo "$id is " . ($status ? 'initialized' : 'not initialized') . PHP_EOL;
     * }
     */
    public static function getLoadedModules(): array
    {
        return self::$loadedModules;
    }

    /**
     * Retrieves a specific module by its identifier.
     *
     * @param string $moduleId The module's unique identifier.
     * @return AbstractModule|null The module instance or null if not found.
     *
     * @example
     * $userModule = ModuleManager::getModule('UserModule');
     * if ($userModule) {
     *     echo $userModule->getDescription();
     * }
     */
    public static function getModule(string $moduleId): ?AbstractModule
    {
        return self::$modules[$moduleId] ?? null;
    }

    /**
     * Initializes all loaded modules by invoking their `boot()` method.
     * Ensures that each module is only initialized once.
     *
     * @example
     * ModuleManager::initializeModules();
     */
    public static function initializeModules(): void
    {
        foreach (self::$modules as $moduleId => $module) {
            if (!isset(self::$loadedModules[$moduleId])) {
                $module->boot();
                self::$loadedModules[$moduleId] = true;
            }
        }
    }
}
