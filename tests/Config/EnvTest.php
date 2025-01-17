<?php

namespace MiniCore\Tests\Config;

use PHPUnit\Framework\TestCase;
use MiniCore\Config\Env;

/**
 * Unit tests for the Env class.
 *
 * This test suite ensures the correct functionality of environment configuration management,
 * including loading `.env` files, retrieving and setting environment variables,
 * and handling errors when the `.env` file is missing.
 *
 * Covered functionality:
 * - Loading environment variables from a file
 * - Fetching environment variables with or without default values
 * - Setting new environment variables
 * - Exception handling for missing environment files
 */
class EnvTest extends TestCase
{
    /**
     * @var string Path to the test environment directory.
     */
    private string $envPath;

    /**
     * Sets up the test environment before each test.
     *
     * Creates the necessary directory for storing `.env` test files.
     */
    protected function setUp(): void
    {
        if (!is_dir(__DIR__ . '/Data')) {
            mkdir(__DIR__ . '/Data');
        }

        $this->envPath = __DIR__ . '/Data/';
    }

    /**
     * Tests successful loading of a `.env` file.
     *
     * Verifies that environment variables are correctly loaded from the `.env.test` file.
     */
    public function testLoadEnvFile()
    {
        Env::load($this->envPath, '/.env.test');

        $this->assertEquals('127.0.0.1', Env::get('DB_HOST'));
        $this->assertEquals('test_user', Env::get('DB_USER'));
        $this->assertEquals('true', Env::get('APP_DEBUG'));
    }

    /**
     * Tests retrieving an environment variable with a default value.
     *
     * Verifies that a default value is returned when the requested key does not exist.
     */
    public function testGetEnvVariableWithDefault()
    {
        $this->assertEquals('default_value', Env::get('NON_EXISTENT_KEY', 'default_value'));
    }

    /**
     * Tests setting a new environment variable.
     *
     * Ensures that a variable can be added to the environment and retrieved successfully.
     */
    public function testSetEnvVariable()
    {
        Env::set('NEW_VAR', 'new_value');

        $this->assertEquals('new_value', Env::get('NEW_VAR'));
        $this->assertEquals('new_value', getenv('NEW_VAR'));
    }

    /**
     * Tests exception handling when the `.env` file does not exist.
     *
     * Verifies that a RuntimeException is thrown if the specified `.env` file is missing.
     */
    public function testLoadNonExistentEnvFile()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Environment file not found');

        Env::load($this->envPath, '.env.nonexistent');
    }

    /**
     * Cleans up the environment variables after each test.
     *
     * Ensures that no environment variables persist between tests.
     */
    protected function tearDown(): void
    {
        unset($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['APP_DEBUG'], $_ENV['NEW_VAR']);
        putenv('NEW_VAR');
    }
}
