<?php

namespace MiniCore\Tests\Config;

use MiniCore\Http\Router;
use PHPUnit\Framework\TestCase;
use MiniCore\Config\RouteLoader;
use Symfony\Component\Yaml\Yaml;
use MiniCore\Tests\Config\Stub\TestRouteHandler;

class RouteLoaderTest extends TestCase
{
    private string $tempConfigPath;

    /**
     * Создание временного YAML файла перед тестами
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
     * Удаление временного файла после тестов
     */
    protected function tearDown(): void
    {
        if (file_exists($this->tempConfigPath)) {
            unlink($this->tempConfigPath);
        }
    }

    /**
     * Тест успешной загрузки маршрута из YAML
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
     * Тест ошибки при отсутствии файла
     */
    public function testLoadNonExistentFile()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Routes file not found');

        RouteLoader::load(__DIR__ . '/Data/nonexistent.yml');
    }

    /**
     * Тест ошибки при некорректной конфигурации маршрута
     */
    public function testInvalidRouteConfiguration()
    {
        $invalidConfigPath = __DIR__ . '/Data/invalid_routes.yml';

        $yamlData = [
            [
                'path' => '/api/example'  // Отсутствует метод
            ]
        ];

        file_put_contents($invalidConfigPath, Yaml::dump($yamlData));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid route configuration');

        RouteLoader::load($invalidConfigPath);

        unlink($invalidConfigPath);
    }

    /**
     * Тест ошибки при отсутствии обработчика
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
     * Тест ошибки, если обработчик не реализует EndpointInterface
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
