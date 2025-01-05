<?php

namespace MiniCore\View;

use Symfony\Component\Yaml\Yaml;
use MiniCore\Module\ModuleManager;

class ViewLoader
{
    private static array $views = []; // Views configuration
    private static array $viewsPaths = []; // Array of views paths for resolving templates

    /**
     * Load views configuration from a YAML file.
     *
     * @param string $configPath Path to the views.yml file.
     * @param string $viewsPath Path to the views directory.
     * @return void
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

        // Merge views
        self::$views = array_merge(self::$views, $data['views']);
        self::$viewsPaths[] = $viewsPath;
    }

    /**
     * Load views configuration from all active modules.
     *
     * @return void
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
     * @param string $viewName The name of the view as defined in views.yml.
     * @param array $data Data to pass to the view.
     * @return string The rendered HTML content.
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

        // Resolve the template path
        $templatePath = self::resolveTemplatePath($template);
        if (!$templatePath) {
            throw new \Exception("Template '$template' not found in registered view paths.");
        }

        // Render the main template
        $content = self::renderTemplate($templatePath, $data);

        // Render with layout if specified
        if ($layout) {
            $layoutPath = self::resolveTemplatePath($layout);
            if (!$layoutPath) {
                throw new \Exception("Layout '$layout' not found in registered view paths.");
            }

            $data['content'] = $content; // Pass the rendered content to the layout
            return self::renderTemplate($layoutPath, $data);
        }

        return $content;
    }

    /**
     * Resolve the full path of a template file from the registered views paths.
     *
     * @param string $template Template file path relative to the views directories.
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
     * @param array $data Data to pass to the template.
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
