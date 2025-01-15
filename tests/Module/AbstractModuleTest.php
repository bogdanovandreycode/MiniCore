<?php

namespace MiniCore\Tests\Module;

use PHPUnit\Framework\TestCase;
use MiniCore\Module\AbstractModule;

/**
 * Заглушка для тестирования AbstractModule
 */
class TestModule extends AbstractModule
{
    public function boot(): void {}
}

class AbstractModuleTest extends TestCase
{
    private TestModule $module;

    protected function setUp(): void
    {
        $this->module = new TestModule(
            'test_module',
            'Test Module',
            'This is a test module',
            'John Doe',
            '1.0.0',
            'MIT'
        );
    }

    /**
     * Тест получения ID модуля
     */
    public function testGetId()
    {
        $this->assertEquals('test_module', $this->module->getId());
    }

    /**
     * Тест получения имени модуля
     */
    public function testGetName()
    {
        $this->assertEquals('Test Module', $this->module->getName());
    }

    /**
     * Тест получения описания модуля
     */
    public function testGetDescription()
    {
        $this->assertEquals('This is a test module', $this->module->getDescription());
    }

    /**
     * Тест получения версии модуля
     */
    public function testGetVersion()
    {
        $this->assertEquals('1.0.0', $this->module->getVersion());
    }

    /**
     * Тест получения пути к модулю
     */
    public function testGetPath()
    {
        $expectedPath = dirname((new \ReflectionClass($this->module))->getFileName());
        $this->assertEquals($expectedPath, $this->module->getPath());
    }
}
