<?php

namespace MiniCore\Tests\Config;

use MiniCore\Http\Router;
use PHPUnit\Framework\TestCase;
use MiniCore\Config\RouteLoader;
use Symfony\Component\Yaml\Yaml;
use MiniCore\Tests\Config\Stub\TestRouteHandler;

/**
 * Unit tests for the RouteLoader class.
 *
 * This test suite verifies the correct loading and validation of route configurations
 * from YAML files into the application's routing system.
 *
 * Covered functionality:
 * - Successful loading of valid routes.
 * - Handling missing configuration files.
 * - Validation of route configurations.
 * - Checking for existing handler classes.
 * - Verifying that handlers implement the required interface.
 */
class RouteLoaderTest extends TestCase
{
    /**
     * @var string Path to the temporary YAML configuration file.
     */
    private string $tempConfigPath;

    /**
     * Sets up the environment before each test.
     *
     * Creates a temporary YAML configuration file for testing route loading.
     */
    protected function setUp(): void
    {
        $this->tempConfigPath = __DIR__ . '/Data/test_routes.yml';

        if (!is_dir(__DIR__ . '/Data')) {
            mkdir(__DIR__ . '/Data');
        }

        $yamlData = [
            [
                'method' => 'GET',
                'path' => '/api/example',
                'handler' => TestRouteHandler::class
            ]
        ];

        file_put_contents($this->tempConfigPath, Yaml::dump($yamlData));
    }

    /**
     * Cleans up the temporary YAML file after each test.
     */
    protected function tearDown(): void
    {
        if (file_exists($this->tempConfigPath)) {
            unlink($this->tempConfigPath);
        }
    }

    /**
     * Tests successful loading of a valid route from a YAML file.
     */
    public function testLoadValidRoute()
    {
        RouteLoader::load($this->tempConfigPath);

        $routes = Router::getRoutes();

        $this->assertArrayHasKey('GET', $routes);
        $this->assertArrayHasKey('/api/example', $routes['GET']);
        $this->assertInstanceOf(TestRouteHandler::class, $routes['GET']['/api/example']);
    }

    /**
     * Tests exception handling when the configuration file does not exist.
     */
    public function testLoadNonExistentFile()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Routes file not found');

        RouteLoader::load(__DIR__ . '/Data/nonexistent.yml');
    }

    /**
     * Tests exception handling for invalid route configuration (missing method).
     */
    public function testInvalidRouteConfiguration()
    {
        $invalidConfigPath = __DIR__ . '/Data/invalid_routes.yml';

        $yamlData = [
            [
                'path' => '/api/example' // Missing 'method' key
            ]
        ];

        file_put_contents($invalidConfigPath, Yaml::dump($yamlData));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid route configuration');

        RouteLoader::load($invalidConfigPath);

        unlink($invalidConfigPath);
    }

    /**
     * Tests exception handling when the handler class does not exist.
     */
    public function testHandlerClassNotFound()
    {
        $invalidHandlerPath = __DIR__ . '/Data/invalid_handler.yml';

        $yamlData = [
            [
                'method' => 'GET',
                'path' => '/api/example',
                'handler' => 'NonExistentClass'
            ]
        ];

        file_put_contents($invalidHandlerPath, Yaml::dump($yamlData));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Handler class not found');

        RouteLoader::load($invalidHandlerPath);

        unlink($invalidHandlerPath);
    }

    /**
     * Tests exception handling when the handler does not implement the required interface.
     */
    public function testHandlerDoesNotImplementInterface()
    {
        $invalidHandlerPath = __DIR__ . '/Data/invalid_interface.yml';

        $yamlData = [
            [
                'method' => 'GET',
                'path' => '/api/example',
                'handler' => \stdClass::class
            ]
        ];

        file_put_contents($invalidHandlerPath, Yaml::dump($yamlData));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Handler must implement EndpointInterface');

        RouteLoader::load($invalidHandlerPath);

        unlink($invalidHandlerPath);
    }
}
