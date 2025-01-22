<?php

namespace MiniCore\Tests\Config\Stub;

use MiniCore\Http\RouteInterface;

/**
 * Stub implementation of the EndpointInterface for testing route loading.
 *
 * This class is used in unit tests to simulate a functional route handler.
 * It provides a predefined response and defines the route configuration for testing.
 *
 * Functionality:
 * - Simulates handling API requests with a static response.
 * - Specifies allowed HTTP methods for the endpoint.
 * - Defines the API route path.
 */
class TestRouteHandler implements RouteInterface
{
    /**
     * Handles incoming API requests and returns a static response.
     *
     * @param array $params Request parameters.
     * @return array The static response indicating successful route handling.
     */
    public function handle(array $params): mixed
    {
        return ['status' => 'route success'];
    }

    /**
     * Returns the allowed HTTP methods for this endpoint.
     *
     * @return array Supported HTTP methods.
     */
    public function getMethods(): array
    {
        return ['GET'];
    }

    /**
     * Returns the route path for this endpoint.
     *
     * @return string The endpoint route.
     */
    public function getRoute(): string
    {
        return '/api/example';
    }
}
