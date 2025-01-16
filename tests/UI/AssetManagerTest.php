<?php

namespace MiniCore\Tests\UI;

use PHPUnit\Framework\TestCase;
use MiniCore\UI\AssetManager;

/**
 * Class AssetManagerTest
 *
 * Tests for the AssetManager class.
 */
class AssetManagerTest extends TestCase
{
    /**
     * Очистка всех стилей и скриптов перед каждым тестом.
     */
    protected function setUp(): void
    {
        AssetManager::clear();
    }

    /**
     * Тест добавления и рендеринга стиля.
     */
    public function testAddStyle(): void
    {
        AssetManager::addStyle('main-style', '/assets/css/style.css');

        $expectedHtml = '<link rel="stylesheet" href="/assets/css/style.css">' . PHP_EOL;

        $this->assertEquals($expectedHtml, AssetManager::renderStyles(), 'CSS стиль не был корректно добавлен или отрендерен.');
    }

    /**
     * Тест предотвращения дублирования стилей.
     */
    public function testAddDuplicateStyle(): void
    {
        AssetManager::addStyle('main-style', '/assets/css/style.css');
        AssetManager::addStyle('main-style', '/assets/css/style.css');

        $expectedHtml = '<link rel="stylesheet" href="/assets/css/style.css">' . PHP_EOL;

        $this->assertEquals($expectedHtml, AssetManager::renderStyles(), 'Дублирование стилей не было предотвращено.');
    }

    /**
     * Тест добавления и рендеринга скрипта в шапке.
     */
    public function testAddScriptToHeader(): void
    {
        AssetManager::addScript('main-script', '/assets/js/app.js');

        $expectedHtml = '<script src="/assets/js/app.js"></script>' . PHP_EOL;

        $this->assertEquals($expectedHtml, AssetManager::renderScripts(), 'JavaScript скрипт для шапки не был корректно добавлен или отрендерен.');
    }

    /**
     * Тест добавления и рендеринга скрипта в подвале.
     */
    public function testAddScriptToFooter(): void
    {
        AssetManager::addScript('footer-script', '/assets/js/footer.js', true);

        $expectedHtml = '<script src="/assets/js/footer.js"></script>' . PHP_EOL;

        $this->assertEquals($expectedHtml, AssetManager::renderScripts(true), 'JavaScript скрипт для подвала не был корректно добавлен или отрендерен.');
    }

    /**
     * Тест предотвращения дублирования скриптов.
     */
    public function testAddDuplicateScript(): void
    {
        AssetManager::addScript('main-script', '/assets/js/app.js');
        AssetManager::addScript('main-script', '/assets/js/app.js');

        $expectedHtml = '<script src="/assets/js/app.js"></script>' . PHP_EOL;

        $this->assertEquals($expectedHtml, AssetManager::renderScripts(), 'Дублирование скриптов не было предотвращено.');
    }

    /**
     * Тест рендера только скриптов для подвала.
     */
    public function testRenderOnlyFooterScripts(): void
    {
        AssetManager::addScript('header-script', '/assets/js/header.js');
        AssetManager::addScript('footer-script', '/assets/js/footer.js', true);

        $expectedFooterHtml = '<script src="/assets/js/footer.js"></script>' . PHP_EOL;

        $this->assertEquals($expectedFooterHtml, AssetManager::renderScripts(true), 'Скрипты для подвала не были корректно отрендерены.');
    }

    /**
     * Тест очистки всех стилей и скриптов.
     */
    public function testClearAssets(): void
    {
        AssetManager::addStyle('style', '/assets/css/style.css');
        AssetManager::addScript('script', '/assets/js/script.js');

        AssetManager::clear();

        $this->assertEmpty(AssetManager::renderStyles(), 'Стили не были очищены.');
        $this->assertEmpty(AssetManager::renderScripts(), 'Скрипты не были очищены.');
    }

    /**
     * Тест рендера без зарегистрированных активов.
     */
    public function testRenderEmptyAssets(): void
    {
        $this->assertEquals('', AssetManager::renderStyles(), 'Рендер пустых стилей должен возвращать пустую строку.');
        $this->assertEquals('', AssetManager::renderScripts(), 'Рендер пустых скриптов должен возвращать пустую строку.');
    }
}
