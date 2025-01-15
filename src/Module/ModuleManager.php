<?php

namespace MiniCore\Module;

use Symfony\Component\Yaml\Yaml;
use MiniCore\Module\AbstractModule;

/**
 * Class ModuleManager
 *
 * Manages the loading, initialization, and access to modules in the MiniCore framework.
 * It reads configuration files, loads module classes, and initializes active modules.
 *
 * @package MiniCore\Module
 *
 * @example
 * // Example usage:
 * try {
 *     ModuleManager::loadModules(__DIR__ . '/config/modules.yml');
 *     ModuleManager::initializeModules();
 *     $userModule = ModuleManager::getModule('UserModule');
 *     echo $userModule->getName(); // Output: User Module
 * } catch (\Exception $e) {
 *     echo 'Error: ' . $e->getMessage();
 * }
 */
class ModuleManager
{
    /**
     * Array of all registered modules.
     *
     * @var AbstractModule[]
     */
    private static array $modules = [];

    /**
     * Array of initialized modules.
     *
     * @var bool[]
     */
    private static array $loadedModules = [];

    /**
     * Loads modules from the specified directory based on the configuration file.
     *
     * @param string $modulesPath Path to the directory containing modules.
     * @param string $configPath Path to the YAML configuration file (modules.yml).
     *
     * @throws \Exception If the configuration file or module directories/classes are missing.
     *
     * @example
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
                continue; // Пропустить отключённые модули
            }

            // 🔥 Убираем проверку директории
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
     * Automatically finds a module class ending with 'Modules\{ModuleName}\Module'.
     *
     * @param string $moduleId
     * @return string|null
     */
    private static function findModuleClass(string $moduleId): ?string
    {
        file_put_contents('test.txt', json_encode(get_declared_classes()));

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
     * @return AbstractModule[] List of registered modules.
     *
     * @example
     * $modules = ModuleManager::getModules();
     * foreach ($modules as $module) {
     *     echo $module->getName() . PHP_EOL;
     * }
     */
    public static function getModules(): array
    {
        return self::$modules;
    }

    /**
     * Returns all initialized (booted) modules.
     *
     * @return bool[] List of initialized modules.
     *
     * @example
     * $initializedModules = ModuleManager::getLoadedModules();
     * foreach ($initializedModules as $moduleId => $status) {
     *     echo "$moduleId is initialized." . PHP_EOL;
     * }
     */
    public static function getLoadedModules(): array
    {
        return self::$loadedModules;
    }

    /**
     * Retrieves a specific module by its ID.
     *
     * @param string $moduleId The unique identifier of the module.
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
     * Initializes all loaded modules by calling their `boot` method.
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
