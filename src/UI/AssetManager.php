<?php

namespace MiniCore\UI;

/**
 * Class AssetManager
 *
 * Handles the registration and rendering of CSS and JavaScript assets in the application.
 * Now supports aliases for asset paths, allowing flexibility in specifying different asset sources.
 *
 * @package MiniCore\UI
 *
 * @example
 * // Defining asset aliases
 * AssetManager::addAlias('core-assets', '/vendor/arraydev/minicore/assets/');
 * AssetManager::addAlias('site-assets', '/assets/');
 * AssetManager::addAlias('admin-module-assets', '/modules/adminpanel/assets/');
 *
 * // Registering a CSS file using an alias
 * AssetManager::addStyle('main-style', '@core-assets/css/style.css');
 *
 * // Registering a JS file using an alias
 * AssetManager::addScript('main-script', '@site-assets/js/app.js', true);
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
     * @var array $aliases Array of registered path aliases.
     */
    private static array $aliases = [];

    /**
     * Add an alias for an asset path.
     *
     * @param string $alias The alias name (e.g., 'core-assets').
     * @param string $path The actual base path.
     * @return void
     *
     * @example
     * AssetManager::addAlias('core-assets', '/vendor/arraydev/minicore/assets/');
     */
    public static function addAlias(string $alias, string $path): void
    {
        self::$aliases[$alias] = rtrim($path, '/') . '/';
    }

    /**
     * Resolve an alias to its actual path.
     *
     * @param string $path The path with an alias (e.g., '@core-assets/css/style.css').
     * @return string The resolved path.
     */
    private static function resolvePath(string $path): string
    {
        if (str_starts_with($path, '@')) {
            $aliasName = substr($path, 1, strpos($path, '/') - 1);
            if (isset(self::$aliases[$aliasName])) {
                return str_replace("@{$aliasName}", self::$aliases[$aliasName], $path);
            }
        }
        return $path;
    }

    /**
     * Add a CSS style to the head section.
     *
     * @param string $handle A unique identifier for the style.
     * @param string $href The URL of the CSS file.
     * @return void
     *
     * @example
     * AssetManager::addStyle('theme', '@core-assets/css/theme.css');
     */
    public static function addStyle(string $handle, string $href): void
    {
        if (!isset(self::$styles[$handle])) {
            self::$styles[$handle] = self::resolvePath($href);
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
     * AssetManager::addScript('analytics', '@site-assets/js/analytics.js', true);
     */
    public static function addScript(string $handle, string $src, bool $inFooter = false): void
    {
        if (!isset(self::$scripts[$handle])) {
            self::$scripts[$handle] = [
                'src' => self::resolvePath($src),
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
