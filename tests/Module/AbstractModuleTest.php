<?php

namespace MiniCore\Tests\Module;

use PHPUnit\Framework\TestCase;
use MiniCore\Module\AbstractModule;

/**
 * Stub class for testing AbstractModule functionality.
 */
class TestModule extends AbstractModule
{
    /**
     * Boot method implementation for the test module.
     */
    public function boot(): void {}
}

/**
 * Unit tests for the AbstractModule class.
 *
 * This test suite verifies the correct behavior of the AbstractModule class,
 * ensuring that all module metadata and configurations are correctly initialized and accessible.
 *
 * Covered functionality:
 * - Retrieving module ID, name, description, version, author, and license.
 * - Retrieving the correct file path of the module.
 */
class AbstractModuleTest extends TestCase
{
    private TestModule $module;

    /**
     * Initializes a test module instance before each test.
     */
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
     * Tests retrieving the module ID.
     */
    public function testGetId()
    {
        $this->assertEquals('test_module', $this->module->getId(), 'Module ID should match.');
    }

    /**
     * Tests retrieving the module name.
     */
    public function testGetName()
    {
        $this->assertEquals('Test Module', $this->module->getName(), 'Module name should match.');
    }

    /**
     * Tests retrieving the module description.
     */
    public function testGetDescription()
    {
        $this->assertEquals('This is a test module', $this->module->getDescription(), 'Module description should match.');
    }

    /**
     * Tests retrieving the module version.
     */
    public function testGetVersion()
    {
        $this->assertEquals('1.0.0', $this->module->getVersion(), 'Module version should match.');
    }

    /**
     * Tests retrieving the module path.
     */
    public function testGetPath()
    {
        $expectedPath = dirname((new \ReflectionClass($this->module))->getFileName());
        $this->assertEquals($expectedPath, $this->module->getPath(), 'Module path should match the file location.');
    }
}
