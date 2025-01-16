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
     * Loads modules from the provided YAML configuration file.
     *
     * Reads the config, finds module classes, and registers enabled modules.
     *
     * @param string $configPath Path to the YAML configuration file (modules.yml).
     *
     * @throws \Exception If the configuration file or module classes are missing.
     *
     * @example
     * // Load modules from configuration
     * ModuleManager::loadModules(__DIR__ . '/config/modules.yml');
     */
    public static function loadModules(string $configPath): void
    {
        if (!file_exists($configPath)) {
            throw new \Exception("Modules configuration file not found: $configPath");
        }

        $config = Yaml::parseFile($configPath)['modules'] ?? [];

        foreach ($config as $moduleId => $moduleConfig) {
            if (!isset($moduleConfig['enabled']) || !$moduleConfig['enabled']) {
                continue;
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
