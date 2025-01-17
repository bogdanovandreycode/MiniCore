<?php

namespace MiniCore\Tests\Module;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use MiniCore\Module\ModuleManager;
use MiniCore\Module\AbstractModule;
use MiniCore\Tests\Module\Modules\TestModule\Module;

/**
 * Unit tests for the ModuleManager class.
 *
 * This test suite verifies the core functionality of the ModuleManager class,
 * ensuring proper loading, initialization, and management of modules.
 *
 * Covered functionality:
 * - Loading modules from YAML configuration.
 * - Initializing loaded modules.
 * - Retrieving modules by ID.
 * - Handling missing configuration files.
 */
class ModuleManagerTest extends TestCase
{
    private string $configPath;

    /**
     * Prepares the test configuration before each test.
     */
    protected function setUp(): void
    {
        $this->configPath = __DIR__ . '/Data/TestModulesConfig.yml';

        if (!is_dir(__DIR__ . '/Data')) {
            mkdir(__DIR__ . '/Data');
        }

        // Creating YAML configuration for the test module
        $yamlData = [
            'modules' => [
                'TestModule' => [
                    'enabled' => true
                ]
            ]
        ];

        // Generating the configuration file
        file_put_contents($this->configPath, Yaml::dump($yamlData));
    }

    /**
     * Tests successful loading of modules.
     */
    public function testLoadModules()
    {
        ModuleManager::loadModules($this->configPath, __DIR__ . '/Modules');

        $modules = ModuleManager::getModules();

        $this->assertArrayHasKey('TestModule', $modules, 'TestModule should be loaded.');
        $this->assertInstanceOf(Module::class, $modules['TestModule'], 'Loaded module should be an instance of Module.');
    }

    /**
     * Tests successful initialization of modules.
     */
    public function testInitializeModules()
    {
        ModuleManager::loadModules($this->configPath, __DIR__ . '/Modules');
        ModuleManager::initializeModules();

        /** @var Module $module */
        $module = ModuleManager::getModule('TestModule');

        $this->assertTrue($module->booted, 'Module should be initialized (booted).');
        $this->assertArrayHasKey('TestModule', ModuleManager::getLoadedModules(), 'TestModule should be in the loaded modules list.');
    }

    /**
     * Tests retrieving a module by its ID.
     */
    public function testGetModule()
    {
        ModuleManager::loadModules($this->configPath, __DIR__ . '/Modules');

        $module = ModuleManager::getModule('TestModule');

        $this->assertInstanceOf(AbstractModule::class, $module, 'Retrieved module should be an instance of AbstractModule.');
        $this->assertEquals('TestModule', $module->getId(), 'Module ID should match the expected value.');
    }

    /**
     * Tests error handling when the configuration file is missing.
     */
    public function testLoadModulesWithMissingConfig()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Modules configuration file not found');

        ModuleManager::loadModules(__DIR__ . '/invalid_config.yml', __DIR__ . '/Modules');
    }
}
