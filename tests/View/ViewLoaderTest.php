<?php

namespace MiniCore\Tests\View;

use PHPUnit\Framework\TestCase;
use MiniCore\View\ViewLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * Unit tests for the ViewLoader class.
 *
 * This test suite verifies the correct behavior of the ViewLoader class,
 * ensuring that view templates and layouts are properly loaded, rendered, and handled.
 *
 * Covered functionality:
 * - Loading view configuration from a YAML file.
 * - Rendering templates with and without layouts.
 * - Handling missing configuration files and templates.
 * - Error handling when templates are not defined.
 */
class ViewLoaderTest extends TestCase
{
    private string $tempConfigPath;
    private string $tempViewsPath;

    /**
     * Creates temporary configuration and view files before each test.
     */
    protected function setUp(): void
    {
        $this->tempConfigPath = __DIR__ . '/Data/test_views.yml';
        $this->tempViewsPath = __DIR__ . '/Data/Views';

        if (!is_dir(__DIR__ . '/Data')) {
            mkdir(__DIR__ . '/Data', 0777, true);
        }

        if (!is_dir($this->tempViewsPath)) {
            mkdir($this->tempViewsPath, 0777, true);
        }

        file_put_contents($this->tempViewsPath . '/template.php', '<h1><?= $title ?></h1>');
        file_put_contents($this->tempViewsPath . '/layout.php', '<div><?= $content ?></div>');

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
     * Deletes temporary files after each test.
     */
    protected function tearDown(): void
    {
        unlink($this->tempConfigPath);
        unlink($this->tempViewsPath . '/template.php');
        unlink($this->tempViewsPath . '/layout.php');
        rmdir($this->tempViewsPath);
    }

    /**
     * Tests loading view configurations from YAML.
     */
    public function testLoadConfig(): void
    {
        ViewLoader::loadConfig($this->tempConfigPath, $this->tempViewsPath);

        $reflection = new \ReflectionClass(ViewLoader::class);
        $property = $reflection->getProperty('views');
        $property->setAccessible(true);
        $views = $property->getValue();

        $this->assertArrayHasKey('home.index', $views, 'View home.index should be loaded.');
        $this->assertArrayHasKey('home.withLayout', $views, 'View home.withLayout should be loaded.');
    }

    /**
     * Tests rendering a template without a layout.
     */
    public function testRenderTemplateWithoutLayout(): void
    {
        ViewLoader::loadConfig($this->tempConfigPath, $this->tempViewsPath);
        $result = ViewLoader::render('home.index', ['title' => 'Hello, World!']);

        $this->assertEquals('<h1>Hello, World!</h1>', trim($result), 'Template should render without layout.');
    }

    /**
     * Tests rendering a template with a layout.
     */
    public function testRenderTemplateWithLayout(): void
    {
        ViewLoader::loadConfig($this->tempConfigPath, $this->tempViewsPath);
        $result = ViewLoader::render('home.withLayout', ['title' => 'Hello, Layout!']);

        $this->assertEquals('<div><h1>Hello, Layout!</h1></div>', trim($result), 'Template should render with layout.');
    }

    /**
     * Tests error handling when the configuration file is missing.
     */
    public function testLoadNonExistentConfig(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Views configuration file not found');

        ViewLoader::loadConfig(__DIR__ . '/nonexistent.yml', $this->tempViewsPath);
    }

    /**
     * Tests error handling when the template file is missing.
     */
    public function testRenderNonExistentTemplate(): void
    {
        $invalidConfigPath = __DIR__ . '/Data/invalid_views.yml';

        $yamlData = [
            'views' => [
                'broken.view' => [
                    'template' => 'nonexistent.php'
                ]
            ]
        ];

        file_put_contents($invalidConfigPath, Yaml::dump($yamlData));

        ViewLoader::loadConfig($invalidConfigPath, $this->tempViewsPath);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Template 'nonexistent.php' not found");

        ViewLoader::render('broken.view');

        unlink($invalidConfigPath);
    }

    /**
     * Tests error handling when the template is missing in the configuration.
     */
    public function testRenderViewWithoutTemplate(): void
    {
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
