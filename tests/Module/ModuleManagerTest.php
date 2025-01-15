<?php

namespace MiniCore\Tests\Module;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use MiniCore\Module\ModuleManager;
use MiniCore\Module\AbstractModule;
use MiniCore\Tests\Module\Modules\TestModule\Module;


class ModuleManagerTest extends TestCase
{
    private string $configPath;

    /**
     * Подготовка конфигурации перед тестами.
     */
    protected function setUp(): void
    {
        $this->configPath = __DIR__ . '/Data/TestModulesConfig.yml';

        // Создание YAML конфигурации для тестового модуля
        $yamlData = [
            'modules' => [
                'TestModule' => [
                    'enabled' => true
                ]
            ]
        ];

        // Генерация конфигурационного файла
        file_put_contents($this->configPath, Yaml::dump($yamlData));
    }

    /**
     * Тест успешной загрузки модуля.
     */
    public function testLoadModules()
    {
        ModuleManager::loadModules($this->configPath);

        $modules = ModuleManager::getModules();

        $this->assertArrayHasKey('TestModule', $modules);
        $this->assertInstanceOf(Module::class, $modules['TestModule']);
    }

    /**
     * Тест инициализации модуля.
     */
    public function testInitializeModules()
    {
        ModuleManager::loadModules($this->configPath);
        ModuleManager::initializeModules();

        /** @var Module $module */
        $module = ModuleManager::getModule('TestModule');

        $this->assertTrue($module->booted, 'Модуль не был инициализирован.');
        $this->assertArrayHasKey('TestModule', ModuleManager::getLoadedModules());
    }

    /**
     * Тест получения модуля по ID.
     */
    public function testGetModule()
    {
        ModuleManager::loadModules($this->configPath);

        $module = ModuleManager::getModule('TestModule');

        $this->assertInstanceOf(AbstractModule::class, $module);
        $this->assertEquals('TestModule', $module->getId());
    }

    /**
     * Тест ошибки при отсутствии конфигурационного файла.
     */
    public function testLoadModulesWithMissingConfig()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Modules configuration file not found');

        ModuleManager::loadModules(__DIR__ . '/invalid_config.yml');
    }
}
