<?php

namespace MiniCore\Tests\View;

use PHPUnit\Framework\TestCase;
use MiniCore\View\ViewLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * Тесты для ViewLoader
 */
class ViewLoaderTest extends TestCase
{
    private string $tempConfigPath;
    private string $tempViewsPath;

    /**
     * Создание временных файлов перед тестами
     */
    protected function setUp(): void
    {
        // Временные пути
        $this->tempConfigPath = __DIR__ . '/Data/test_views.yml';
        $this->tempViewsPath = __DIR__ . '/Data/Views';

        // Создание директории для шаблонов
        if (!is_dir(__DIR__ . '/Data')) {
            mkdir(__DIR__ . '/Data', 0777, true);
        }

        // Создание директории для шаблонов
        if (!is_dir($this->tempViewsPath)) {
            mkdir($this->tempViewsPath, 0777, true);
        }

        // Создание шаблона
        file_put_contents($this->tempViewsPath . '/template.php', '<h1><?= $title ?></h1>');
        file_put_contents($this->tempViewsPath . '/layout.php', '<div><?= $content ?></div>');

        // YAML конфигурация
        $yamlData = [
            'views' => [
                'home.index' => [
                    'template' => 'template.php',
                ],
                'home.withLayout' => [
                    'template' => 'template.php',
                    'layout' => 'layout.php',
                ],
            ]
        ];

        file_put_contents($this->tempConfigPath, Yaml::dump($yamlData));
    }

    /**
     * Удаление временных файлов после тестов
     */
    protected function tearDown(): void
    {
        unlink($this->tempConfigPath);
        unlink($this->tempViewsPath . '/template.php');
        unlink($this->tempViewsPath . '/layout.php');
        rmdir($this->tempViewsPath);
    }

    /**
     * Тест загрузки конфигурации представлений
     */
    public function testLoadConfig()
    {
        ViewLoader::loadConfig($this->tempConfigPath, $this->tempViewsPath);

        $reflection = new \ReflectionClass(ViewLoader::class);
        $property = $reflection->getProperty('views');
        $property->setAccessible(true);
        $views = $property->getValue();

        $this->assertArrayHasKey('home.index', $views);
        $this->assertArrayHasKey('home.withLayout', $views);
    }

    /**
     * Тест рендера представления без лэйаута
     */
    public function testRenderTemplateWithoutLayout()
    {
        ViewLoader::loadConfig($this->tempConfigPath, $this->tempViewsPath);
        $result = ViewLoader::render('home.index', ['title' => 'Hello, World!']);

        $this->assertEquals('<h1>Hello, World!</h1>', trim($result));
    }

    /**
     * Тест рендера представления с лэйаутом
     */
    public function testRenderTemplateWithLayout()
    {
        ViewLoader::loadConfig($this->tempConfigPath, $this->tempViewsPath);
        $result = ViewLoader::render('home.withLayout', ['title' => 'Hello, Layout!']);

        $this->assertEquals('<div><h1>Hello, Layout!</h1></div>', trim($result));
    }

    /**
     * Тест ошибки при отсутствии конфигурационного файла
     */
    public function testLoadNonExistentConfig()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Views configuration file not found');

        ViewLoader::loadConfig(__DIR__ . '/nonexistent.yml', $this->tempViewsPath);
    }

    /**
     * Тест ошибки при отсутствии шаблона
     */
    public function testRenderNonExistentTemplate()
    {
        // Создание конфигурации с несуществующим шаблоном
        $invalidConfigPath = __DIR__ . '/Data/invalid_views.yml';

        $yamlData = [
            'views' => [
                'broken.view' => [
                    'template' => 'nonexistent.php'
                ]
            ]
        ];

        file_put_contents($invalidConfigPath, Yaml::dump($yamlData));

        // Загрузка конфигурации
        ViewLoader::loadConfig($invalidConfigPath, $this->tempViewsPath);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Template 'nonexistent.php' not found");

        // Рендеринг шаблона с несуществующим файлом
        ViewLoader::render('broken.view');

        unlink($invalidConfigPath);
    }


    /**
     * Тест ошибки при отсутствии шаблона в конфигурации
     */
    public function testRenderViewWithoutTemplate()
    {
        // Конфигурация без template
        $invalidConfigPath = __DIR__ . '/Data/invalid_views.yml';
        $yamlData = [
            'views' => [
                'broken.view' => []
            ]
        ];
        file_put_contents($invalidConfigPath, Yaml::dump($yamlData));

        ViewLoader::loadConfig($invalidConfigPath, $this->tempViewsPath);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("No template defined for view 'broken.view'");

        ViewLoader::render('broken.view');

        unlink($invalidConfigPath);
    }
}
