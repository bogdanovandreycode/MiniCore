<?php

namespace MiniCore\Tests\UI;

use PHPUnit\Framework\TestCase;
use MiniCore\UI\AssetManager;

/**
 * Unit tests for the AssetManager class.
 *
 * This test suite verifies the core functionality of the AssetManager class,
 * ensuring that styles and scripts are correctly managed, rendered, and cleared.
 *
 * Covered functionality:
 * - Adding and rendering CSS styles.
 * - Adding and rendering JavaScript scripts (header and footer).
 * - Preventing duplication of styles and scripts.
 * - Selective rendering of footer scripts.
 * - Clearing all registered assets.
 * - Rendering with no registered assets.
 */
class AssetManagerTest extends TestCase
{
    /**
     * Clears all styles and scripts before each test to ensure a clean state.
     */
    protected function setUp(): void
    {
        AssetManager::clear();
    }

    /**
     * Tests adding and rendering a CSS style.
     */
    public function testAddStyle(): void
    {
        AssetManager::addStyle('main-style', '/assets/css/style.css');

        $expectedHtml = '<link rel="stylesheet" href="/assets/css/style.css">' . PHP_EOL;

        $this->assertEquals($expectedHtml, AssetManager::renderStyles(), 'CSS style was not added or rendered correctly.');
    }

    /**
     * Tests preventing duplication of CSS styles.
     */
    public function testAddDuplicateStyle(): void
    {
        AssetManager::addStyle('main-style', '/assets/css/style.css');
        AssetManager::addStyle('main-style', '/assets/css/style.css');

        $expectedHtml = '<link rel="stylesheet" href="/assets/css/style.css">' . PHP_EOL;

        $this->assertEquals($expectedHtml, AssetManager::renderStyles(), 'Duplicate styles were not prevented.');
    }

    /**
     * Tests adding and rendering a JavaScript script in the header.
     */
    public function testAddScriptToHeader(): void
    {
        AssetManager::addScript('main-script', '/assets/js/app.js');

        $expectedHtml = '<script src="/assets/js/app.js"></script>' . PHP_EOL;

        $this->assertEquals($expectedHtml, AssetManager::renderScripts(), 'Header JavaScript script was not added or rendered correctly.');
    }

    /**
     * Tests adding and rendering a JavaScript script in the footer.
     */
    public function testAddScriptToFooter(): void
    {
        AssetManager::addScript('footer-script', '/assets/js/footer.js', true);

        $expectedHtml = '<script src="/assets/js/footer.js"></script>' . PHP_EOL;

        $this->assertEquals($expectedHtml, AssetManager::renderScripts(true), 'Footer JavaScript script was not added or rendered correctly.');
    }

    /**
     * Tests preventing duplication of JavaScript scripts.
     */
    public function testAddDuplicateScript(): void
    {
        AssetManager::addScript('main-script', '/assets/js/app.js');
        AssetManager::addScript('main-script', '/assets/js/app.js');

        $expectedHtml = '<script src="/assets/js/app.js"></script>' . PHP_EOL;

        $this->assertEquals($expectedHtml, AssetManager::renderScripts(), 'Duplicate scripts were not prevented.');
    }

    /**
     * Tests rendering only footer scripts.
     */
    public function testRenderOnlyFooterScripts(): void
    {
        AssetManager::addScript('header-script', '/assets/js/header.js');
        AssetManager::addScript('footer-script', '/assets/js/footer.js', true);

        $expectedFooterHtml = '<script src="/assets/js/footer.js"></script>' . PHP_EOL;

        $this->assertEquals($expectedFooterHtml, AssetManager::renderScripts(true), 'Footer scripts were not rendered correctly.');
    }

    /**
     * Tests clearing all registered styles and scripts.
     */
    public function testClearAssets(): void
    {
        AssetManager::addStyle('style', '/assets/css/style.css');
        AssetManager::addScript('script', '/assets/js/script.js');

        AssetManager::clear();

        $this->assertEmpty(AssetManager::renderStyles(), 'Styles were not cleared.');
        $this->assertEmpty(AssetManager::renderScripts(), 'Scripts were not cleared.');
    }

    /**
     * Tests rendering when no assets are registered (should return empty strings).
     */
    public function testRenderEmptyAssets(): void
    {
        $this->assertEquals('', AssetManager::renderStyles(), 'Rendering empty styles should return an empty string.');
        $this->assertEquals('', AssetManager::renderScripts(), 'Rendering empty scripts should return an empty string.');
    }
}
