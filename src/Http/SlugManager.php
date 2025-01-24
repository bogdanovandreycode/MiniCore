<?php

namespace MiniCore\Http;

use MiniCore\Http\Router;

/**
 * Class SlugManager
 *
 * Manages slugs and their mapping to original URLs. 
 * Allows registration of slugs and automatically registers routes via the Router.
 */
class SlugManager
{
    /**
     * Stores registered slugs.
     *
     * @var array
     * [
     *     'slug' => 'original_url',
     *     ...
     * ]
     */
    private static array $slugs = [];

    /**
     * Registers a slug and its associated route.
     *
     * @param string $slug The slug (e.g., 'new2' or '2025/new2').
     * @param string $originalUrl The original route (e.g., '/post/?id=2').
     * @param RouteInterface $handler The route handler.
     *
     * @return void
     *
     * @throws \RuntimeException If the slug is already registered.
     *
     * @example
     * // Handling duplicate slugs
     * SlugManager::register('post/new2', '/post/?id=2', new PostController());
     * SlugManager::register('post/new2', '/post/?id=3', new PostController());
     * // Throws RuntimeException: Slug 'post/new2' is already registered.
     */
    public static function register(string $slug, string $originalUrl, RouteInterface $handler): void
    {
        if (isset(self::$slugs[$slug])) {
            throw new \RuntimeException("Slug '$slug' is already registered.");
        }

        self::$slugs[$slug] = $originalUrl;

        Router::register("/$slug", $handler);
    }

    /**
     * Retrieves the original route by slug.
     *
     * @param string $slug The slug to search for.
     *
     * @return string|null The original route, or null if the slug is not found.
     *
     * @example
     * $originalUrl = SlugManager::getOriginalUrl('post/new2');
     * echo $originalUrl; // Output: /post/?id=2
     */
    public static function getOriginalUrl(string $slug): ?string
    {
        return self::$slugs[$slug] ?? null;
    }

    /**
     * Retrieves all registered slugs.
     *
     * @return array An associative array of slugs and their corresponding original routes.
     *
     * @example
     * $slugs = SlugManager::getSlugs();
     * print_r($slugs);
     * // Output:
     * // [
     * //     'post/new2' => '/post/?id=2',
     * //     'post/2025/new2' => '/post/?id=2',
     * // ]
     */
    public static function getSlugs(): array
    {
        return self::$slugs;
    }
}
