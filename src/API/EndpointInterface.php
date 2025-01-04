<?php

namespace Vendor\Undermarket\Core\API;

interface EndpointInterface
{
    /**
     * Handle the incoming request and return a response.
     *
     * @param array $params Parameters extracted from the request (e.g., query or route parameters).
     * @return mixed The response (e.g., JSON, HTML, etc.).
     */
    public function handle(array $params): mixed;

    /**
     * Get the HTTP method(s) supported by this endpoint (e.g., GET, POST).
     *
     * @return array List of supported HTTP methods.
     */
    public function getMethods(): array;

    /**
     * Get the route associated with this endpoint.
     *
     * @return string The route string (e.g., "/api/users").
     */
    public function getRoute(): string;
}
