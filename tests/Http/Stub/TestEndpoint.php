<?php

namespace MiniCore\Tests\Http\Stub;

use MiniCore\API\EndpointInterface;
use MiniCore\API\RestEndpoint;

/**
 * Stub implementation of the EndpointInterface for testing purposes.
 *
 * This class simulates a basic API endpoint and is used in unit tests to verify
 * the correct behavior of route and endpoint loaders. It returns a predefined
 * response and defines basic routing information.
 *
 * Functionality:
 * - Handles API requests by returning a static success response.
 * - Specifies the HTTP method allowed for the endpoint.
 * - Defines the API route path.
 */
class TestEndpoint extends RestEndpoint implements EndpointInterface
{
    /**
     * Handles the incoming request and returns a static success response.
     *
     * @param array $params Request parameters.
     * @return array The static success response.
     */
    public function handle(array $params): mixed
    {
        return ['status' => 'success'];
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
        return '/api/test';
    }
}
