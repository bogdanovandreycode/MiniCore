<?php

namespace MiniCore\UI;

/**
 * Class AssetManager
 *
 * Handles the registration and rendering of CSS and JavaScript assets in the application.
 * This class simplifies the process of adding styles and scripts to the front-end by managing them centrally.
 *
 * @package MiniCore\UI
 *
 * @example
 * // Registering a CSS file
 * AssetManager::addStyle('main-style', '/assets/css/style.css');
 *
 * // Registering a JS file to load in the footer
 * AssetManager::addScript('main-script', '/assets/js/app.js', true);
 *
 * // Rendering all registered styles in the <head>
 * echo AssetManager::renderStyles();
 *
 * // Rendering footer scripts before </body>
 * echo AssetManager::renderScripts(true);
 */
class AssetManager
{
    /**
     * @var array $styles Array of registered CSS styles.
     */
    private static array $styles = [];

    /**
     * @var array $scripts Array of registered JS scripts.
     */
    private static array $scripts = [];

    /**
     * Add a CSS style to the head section.
     *
     * @param string $handle A unique identifier for the style.
     * @param string $href The URL of the CSS file.
     * @return void
     *
     * @example
     * AssetManager::addStyle('theme', '/assets/css/theme.css');
     */
    public static function addStyle(string $handle, string $href): void
    {
        if (!isset(self::$styles[$handle])) {
            self::$styles[$handle] = $href;
        }
    }

    /**
     * Add a JavaScript script to the head or footer section.
     *
     * @param string $handle A unique identifier for the script.
     * @param string $src The URL of the JS file.
     * @param bool $inFooter Whether the script should be added to the footer.
     * @return void
     *
     * @example
     * // Add script to the footer
     * AssetManager::addScript('analytics', '/assets/js/analytics.js', true);
     */
    public static function addScript(string $handle, string $src, bool $inFooter = false): void
    {
        if (!isset(self::$scripts[$handle])) {
            self::$scripts[$handle] = [
                'src' => $src,
                'in_footer' => $inFooter,
            ];
        }
    }

    /**
     * Render all registered CSS styles as HTML <link> tags.
     *
     * @return string The HTML for all registered styles.
     *
     * @example
     * // Output all styles in the <head> section
     * echo AssetManager::renderStyles();
     */
    public static function renderStyles(): string
    {
        $html = '';
        foreach (self::$styles as $href) {
            $html .= sprintf('<link rel="stylesheet" href="%s">' . PHP_EOL, htmlspecialchars($href));
        }
        return $html;
    }

    /**
     * Render all registered JavaScript scripts as HTML <script> tags.
     *
     * @param bool $inFooter Whether to render only footer scripts.
     * @return string The HTML for all registered scripts.
     *
     * @example
     * // Output scripts for the footer before </body>
     * echo AssetManager::renderScripts(true);
     */
    public static function renderScripts(bool $inFooter = false): string
    {
        $html = '';
        foreach (self::$scripts as $script) {
            if ($script['in_footer'] === $inFooter) {
                $html .= sprintf('<script src="%s"></script>' . PHP_EOL, htmlspecialchars($script['src']));
            }
        }
        return $html;
    }

    /**
     * Clear all registered styles and scripts.
     *
     * @return void
     *
     * @example
     * // Remove all registered assets
     * AssetManager::clear();
     */
    public static function clear(): void
    {
        self::$styles = [];
        self::$scripts = [];
    }
}
