<?php

namespace MiniCore\Tests\Config;

use MiniCore\Http\Router;
use PHPUnit\Framework\TestCase;
use MiniCore\Http\RestApiRouter;
use Symfony\Component\Yaml\Yaml;
use MiniCore\Config\RestEndpointLoader;
use MiniCore\Tests\Config\Stub\TestEndpoint;

/**
 * Unit tests for the RestEndpointLoader class.
 *
 * This test suite ensures the proper loading and validation of REST endpoints from YAML configuration files.
 *
 * Covered functionality:
 * - Loading valid endpoints from YAML files.
 * - Handling missing configuration files.
 * - Validating endpoint configuration keys.
 * - Checking for existing handler classes.
 * - Verifying handler implementation of the required interface.
 */
class RestEndpointLoaderTest extends TestCase
{
    /**
     * @var string Path to the temporary YAML configuration file.
     */
    private string $tempConfigPath;

    /**
     * @var string Path to the temporary .env file.
     */
    private string $envPath;

    /**
     * Sets up the environment before each test.
     *
     * Creates a temporary YAML file and a `.env` file required for testing.
     */
    protected function setUp(): void
    {
        $this->tempConfigPath = __DIR__ . '/Data/test_endpoints.yml';

        if (!is_dir(__DIR__ . '/Data')) {
            mkdir(__DIR__ . '/Data');
        }

        $this->envPath = __DIR__ . '/Data/.env.test';

        // Create .env.test file if it doesn't exist
        if (!file_exists($this->envPath)) {
            $envData = [
                'DB_HOST' => '127.0.0.1',
                'DB_USER' => 'test_user',
                'APP_DEBUG' => 'true'
            ];

            $envContent = implode("\n", array_map(fn($key, $value) => "$key=$value", array_keys($envData), $envData));
            file_put_contents($this->envPath, $envContent);
        }

        $yamlData = [
            'endpoints' => [
                [
                    'method' => 'GET',
                    'route' => '/api/test',
                    'handler' => TestEndpoint::class
                ]
            ]
        ];

        file_put_contents($this->tempConfigPath, Yaml::dump($yamlData));
    }

    /**
     * Cleans up temporary files after each test.
     */
    protected function tearDown(): void
    {
        if (file_exists($this->tempConfigPath)) {
            unlink($this->tempConfigPath);
        }
    }

    /**
     * Tests successful loading of a valid endpoint from a YAML file.
     */
    /**
     * Tests successful loading of a valid endpoint from a YAML file.
     */
    public function testLoadValidEndpoint(): void
    {
        // Загружаем маршруты из временного файла
        RestEndpointLoader::load($this->tempConfigPath);

        // Получаем зарегистрированные маршруты
        $routes = RestApiRouter::getRoutes();

        // Проверяем структуру маршрутов
        $this->assertArrayHasKey('GET', $routes);
        $this->assertArrayHasKey('/api/test', $routes['GET']);

        // Проверяем, что endpoint корректно зарегистрирован
        $this->assertInstanceOf(TestEndpoint::class, $routes['GET']['/api/test']['endpoint']);
    }

    /**
     * Tests exception handling when the configuration file is missing.
     */
    public function testLoadNonExistentFile()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Endpoints file not found');

        RestEndpointLoader::load(__DIR__ . '/Data/nonexistent.yml');
    }

    /**
     * Tests exception handling for invalid endpoint configuration (missing method).
     */
    public function testInvalidEndpointConfiguration()
    {
        $invalidConfigPath = __DIR__ . '/Data/invalid_endpoints.yml';

        $yamlData = [
            'endpoints' => [
                [
                    'route' => '/api/test' // Missing 'method' key
                ]
            ]
        ];

        file_put_contents($invalidConfigPath, Yaml::dump($yamlData));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid endpoint configuration');

        RestEndpointLoader::load($invalidConfigPath);

        unlink($invalidConfigPath);
    }

    /**
     * Tests exception handling when the handler class does not exist.
     */
    public function testHandlerClassNotFound()
    {
        $invalidHandlerPath = __DIR__ . '/Data/invalid_handler.yml';

        $yamlData = [
            'endpoints' => [
                [
                    'method' => 'GET',
                    'route' => '/api/test',
                    'handler' => 'NonExistentClass'
                ]
            ]
        ];

        file_put_contents($invalidHandlerPath, Yaml::dump($yamlData));
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Handler class not found');
        RestEndpointLoader::load($invalidHandlerPath);
        unlink($invalidHandlerPath);
    }

    /**
     * Tests exception handling when the handler does not implement the required interface.
     */
    public function testHandlerDoesNotImplementInterface()
    {
        $invalidHandlerPath = __DIR__ . '/Data/invalid_interface.yml';

        $yamlData = [
            'endpoints' => [
                [
                    'method' => 'GET',
                    'route' => '/api/test',
                    'handler' => \stdClass::class
                ]
            ]
        ];

        file_put_contents($invalidHandlerPath, Yaml::dump($yamlData));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Handler must implement EndpointInterface');

        RestEndpointLoader::load($invalidHandlerPath);

        unlink($invalidHandlerPath);
    }
}
