<?php

namespace MiniCore\Tests\Config;

use MiniCore\Http\Router;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use MiniCore\Config\RestEndpointLoader;
use MiniCore\Tests\Config\Stub\TestEndpoint;

class RestEndpointLoaderTest extends TestCase
{
    private string $tempConfigPath;

    /**
     * Создание временного YAML файла перед тестами
     */
    protected function setUp(): void
    {
        $this->tempConfigPath = __DIR__ . '/Data/test_endpoints.yml';

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
     * Удаление временного файла после тестов
     */
    protected function tearDown(): void
    {
        if (file_exists($this->tempConfigPath)) {
            unlink($this->tempConfigPath);
        }
    }

    /**
     * Тест успешной загрузки эндпоинта из YAML
     */
    public function testLoadValidEndpoint()
    {
        RestEndpointLoader::load($this->tempConfigPath);

        $routes = Router::getRoutes();

        $this->assertArrayHasKey('GET', $routes);
        $this->assertArrayHasKey('/api/test', $routes['GET']);
        $this->assertInstanceOf(TestEndpoint::class, $routes['GET']['/api/test']);
    }

    /**
     * Тест ошибки при отсутствии файла
     */
    public function testLoadNonExistentFile()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Endpoints file not found');

        RestEndpointLoader::load(__DIR__ . '/Data/nonexistent.yml');
    }

    /**
     * Тест ошибки при отсутствии ключей в конфигурации
     */
    public function testInvalidEndpointConfiguration()
    {
        $invalidConfigPath = __DIR__ . '/Data/invalid_endpoints.yml';

        $yamlData = [
            'endpoints' => [
                [
                    'route' => '/api/test'  // Метод отсутствует
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
     * Тест ошибки при отсутствии обработчика
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
     * Тест ошибки, если обработчик не реализует EndpointInterface
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
