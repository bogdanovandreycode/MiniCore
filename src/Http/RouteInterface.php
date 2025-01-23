<?php

namespace MiniCore\Http;

/**
 * Interface RouteInterface
 *
 * Defines the contract for general route-related functionality.
 */
interface RouteInterface
{
    /**
     * Get the route associated with this handler.
     *
     * @return string The route string.
     */
    public function getRoute(): string;

    /**
     * Handle the incoming request and return a response.
     *
     * @param array $params Parameters extracted from the request.
     * @return mixed The response data.
     */
    public function handle(array $params): mixed;
}
