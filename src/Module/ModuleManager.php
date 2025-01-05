<?php

namespace MiniCore\Module;

use Symfony\Component\Yaml\Yaml;
use MiniCore\Module\AbstractModule;

class ModuleManager
{
    private static array $modules = []; // Registered modules
    private static array $loadedModules = []; // Initialized modules

    /**
     *Loads modules from the specified path and configuration file.
     *
     * @param string $modulesPath Path to the modules directory.
     * @param string $configPath Path to the modules configuration file (modules.yml).
     */
    public static function loadModules(string $modulesPath, string $configPath): void
    {
        if (!file_exists($configPath)) {
            throw new \Exception("Modules configuration file not found: $configPath");
        }

        $config = Yaml::parseFile($configPath)['modules'] ?? [];

        foreach ($config as $moduleId => $moduleConfig) {
            if (!isset($moduleConfig['enabled']) || !$moduleConfig['enabled']) {
                continue;
            }

            $modulePath = $modulesPath . DIRECTORY_SEPARATOR . $moduleId;

            if (!is_dir($modulePath)) {
                throw new \Exception("Module directory not found: $modulePath");
            }

            $bootFile = $modulePath . DIRECTORY_SEPARATOR . 'Boot.php';

            if (!file_exists($bootFile)) {
                throw new \Exception("Boot file not found for module '$moduleId'.");
            }

            require_once $bootFile;
            $className = "Modules\\$moduleId\\{$moduleId}Module";

            if (!class_exists($className)) {
                throw new \Exception("Module class '$className' not found in '$moduleId'.");
            }

            /** @var AbstractModule $module */
            $module = new $className();
            self::$modules[$moduleId] = $module;
        }
    }

    public static function getModules(): array
    {
        return self::$modules;
    }

    public static function getLoadedModules(): array
    {
        return self::$loadedModules;
    }

    public static function getModule(string $moduleId): ?AbstractModule
    {
        return self::$modules[$moduleId] ?? null;
    }

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
