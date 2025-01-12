<?php

namespace MiniCore\View;

use Symfony\Component\Yaml\Yaml;
use MiniCore\Module\ModuleManager;

/**
 * Class ViewLoader
 *
 * Handles loading, managing, and rendering view templates in the application.
 * It supports loading views from configuration files (`views.yml`) and dynamically loading views from active modules.
 * Additionally, it supports rendering templates with or without layouts, making it a flexible solution for managing UI templates.
 *
 * @package MiniCore\View
 *
 * @example
 * // Loading views configuration
 * ViewLoader::loadConfig('/path/to/views.yml', '/path/to/views');
 *
 * // Rendering a view
 * echo ViewLoader::render('home.index', ['title' => 'Welcome']);
 */
class ViewLoader
{
    /**
     * @var array $views Loaded views configuration.
     */
    private static array $views = [];

    /**
     * @var array $viewsPaths Registered paths to search for view templates.
     */
    private static array $viewsPaths = [];

    /**
     * Load views configuration from a YAML file.
     *
     * @param string $configPath Path to the `views.yml` file.
     * @param string $viewsPath Path to the views directory.
     * @return void
     *
     * @throws \Exception If the configuration file or views path does not exist.
     *
     * @example
     * ViewLoader::loadConfig('/app/Config/views.yml', '/app/Views');
     */
    public static function loadConfig(string $configPath, string $viewsPath): void
    {
        if (!file_exists($configPath)) {
            throw new \Exception("Views configuration file not found: $configPath");
        }

        $data = Yaml::parseFile($configPath);

        if (!isset($data['views'])) {
            throw new \Exception("Invalid views configuration file: missing 'views' section.");
        }

        if (!is_dir($viewsPath)) {
            throw new \Exception("Views path is not a directory or does not exist: $viewsPath");
        }

        self::$views = array_merge(self::$views, $data['views']);
        self::$viewsPaths[] = $viewsPath;
    }

    /**
     * Load views configuration from all active modules.
     *
     * @return void
     *
     * @example
     * // Load views from all active modules
     * ViewLoader::loadFromModules();
     */
    public static function loadFromModules(): void
    {
        foreach (ModuleManager::getModules() as $moduleId => $module) {
            $configPath = $module->getPath() . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'views.yml';
            $viewsPath = $module->getPath() . DIRECTORY_SEPARATOR . 'Views';

            if (file_exists($configPath) && is_dir($viewsPath)) {
                self::loadConfig($configPath, $viewsPath);
            }
        }
    }

    /**
     * Render a view with optional layout.
     *
     * @param string $viewName The view identifier defined in `views.yml`.
     * @param array $data Data to pass to the view template.
     * @return string The rendered HTML content.
     *
     * @throws \Exception If the view or template is not found.
     *
     * @example
     * echo ViewLoader::render('blog.post', ['title' => 'My Blog Post']);
     */
    public static function render(string $viewName, array $data = []): string
    {
        if (!isset(self::$views[$viewName])) {
            throw new \Exception("View '$viewName' not found in views configuration.");
        }

        $viewConfig = self::$views[$viewName];
        $template = $viewConfig['template'] ?? null;
        $layout = $viewConfig['layout'] ?? null;

        if (!$template) {
            throw new \Exception("No template defined for view '$viewName'.");
        }

        $templatePath = self::resolveTemplatePath($template);
        if (!$templatePath) {
            throw new \Exception("Template '$template' not found in registered view paths.");
        }

        $content = self::renderTemplate($templatePath, $data);

        if ($layout) {
            $layoutPath = self::resolveTemplatePath($layout);
            if (!$layoutPath) {
                throw new \Exception("Layout '$layout' not found in registered view paths.");
            }

            $data['content'] = $content;
            return self::renderTemplate($layoutPath, $data);
        }

        return $content;
    }

    /**
     * Resolve the full path of a template file from the registered views paths.
     *
     * @param string $template Template file relative path.
     * @return string|null Full path to the template or null if not found.
     */
    private static function resolveTemplatePath(string $template): ?string
    {
        foreach (self::$viewsPaths as $viewsPath) {
            $templateFile = $viewsPath . DIRECTORY_SEPARATOR . $template;
            if (file_exists($templateFile)) {
                return $templateFile;
            }
        }
        return null;
    }

    /**
     * Render a specific template file.
     *
     * @param string $templateFile Full path to the template file.
     * @param array $data Data to extract into the template.
     * @return string The rendered content.
     */
    private static function renderTemplate(string $templateFile, array $data): string
    {
        extract($data);
        ob_start();
        include $templateFile;
        return ob_get_clean();
    }
}
