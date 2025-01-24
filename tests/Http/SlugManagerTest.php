<?php

use PHPUnit\Framework\TestCase;
use MiniCore\Http\SlugManager;
use MiniCore\Http\Router;
use MiniCore\Http\RouteInterface;

/**
 * Class SlugManagerTest
 *
 * Unit tests for the SlugManager class.
 */
class SlugManagerTest extends TestCase
{
    /**
     * Test registering a new slug.
     *
     * @return void
     */
    public function testRegisterSlug(): void
    {
        $mockHandler = $this->createMock(RouteInterface::class);

        SlugManager::register('post/new2', '/post/?id=2', $mockHandler);

        $this->assertArrayHasKey('post/new2', SlugManager::getSlugs());
        $this->assertEquals('/post/?id=2', SlugManager::getOriginalUrl('post/new2'));
    }

    /**
     * Test attempting to register a duplicate slug.
     *
     * @return void
     * @throws RuntimeException Expected exception if the slug is already registered.
     */
    public function testRegisterDuplicateSlug(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Slug 'post/new2' is already registered.");

        $mockHandler = $this->createMock(RouteInterface::class);

        SlugManager::register('post/new2', '/post/?id=2', $mockHandler);
        SlugManager::register('post/new2', '/post/?id=3', $mockHandler); // Duplicate
    }

    /**
     * Test retrieving the original URL of a registered slug.
     *
     * @return void
     */
    public function testGetOriginalUrl(): void
    {
        $mockHandler = $this->createMock(RouteInterface::class);

        SlugManager::register('post/new3', '/post/?id=3', $mockHandler);

        $originalUrl = SlugManager::getOriginalUrl('post/new3');
        $this->assertEquals('/post/?id=3', $originalUrl);

        $this->assertNull(SlugManager::getOriginalUrl('post/nonexistent'));
    }

    /**
     * Test retrieving all registered slugs.
     *
     * @return void
     */
    public function testGetSlugs(): void
    {
        $mockHandler = $this->createMock(RouteInterface::class);

        SlugManager::register('post/new4', '/post/?id=4', $mockHandler);
        SlugManager::register('post/2025/new2', '/post/?id=3', $mockHandler);

        $slugs = SlugManager::getSlugs();

        $this->assertArrayHasKey('post/new4', $slugs);
        $this->assertArrayHasKey('post/2025/new2', $slugs);

        $this->assertEquals('/post/?id=4', $slugs['post/new4']);
        $this->assertEquals('/post/?id=3', $slugs['post/2025/new2']);
    }
}
