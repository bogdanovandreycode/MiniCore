<?php

namespace Vendor\Undermarket\Core\UI;

class AssetManager
{
    private static array $styles = []; // Массив зарегистрированных стилей
    private static array $scripts = []; // Массив зарегистрированных скриптов

    /**
     * Add a CSS style to the head section.
     *
     * @param string $handle A unique identifier for the style.
     * @param string $href The URL of the CSS file.
     * @return void
     */
    public static function addStyle(string $handle, string $href): void
    {
        if (!isset(self::$styles[$handle])) {
            self::$styles[$handle] = $href;
        }
    }

    /**
     * Add a JS script to the head or footer section.
     *
     * @param string $handle A unique identifier for the script.
     * @param string $src The URL of the JS file.
     * @param bool $inFooter Whether the script should be added to the footer.
     * @return void
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
     * Render all styles as HTML <link> tags.
     *
     * @return string The HTML for all styles.
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
     * Render all scripts as HTML <script> tags.
     *
     * @param bool $inFooter Whether to render only footer scripts.
     * @return string The HTML for all scripts.
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
     * Clear all registered assets.
     *
     * @return void
     */
    public static function clear(): void
    {
        self::$styles = [];
        self::$scripts = [];
    }
}
