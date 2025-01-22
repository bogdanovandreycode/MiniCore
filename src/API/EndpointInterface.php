<?php

namespace MiniCore\API;

/**
 * Interface EndpointInterface
 *
 * Defines the contract for REST API endpoints.
 * Any class that implements this interface can be used as a REST API handler
 * by responding to HTTP requests with specified methods and routes.
 *
 * @example
 * // Example of a simple endpoint implementation:
 * use MiniCore\API\EndpointInterface;
 *
 * class GetUsersEndpoint implements EndpointInterface
 * {
 *     public function handle(array $params): mixed
 *     {
 *         // Example response
 *         return ['users' => [['id' => 1, 'name' => 'John Doe']]];
 *     }
 *
 *     public function getMethods(): array
 *     {
 *         return ['GET'];
 *     }
 *
 *     public function getRoute(): string
 *     {
 *         return '/api/users';
 *     }
 * }
 */
interface EndpointInterface
{
    /**
     * Handle the incoming request and return a response.
     *
     * This method is called when a request matches the endpoint's route and method.
     * It should contain the core logic for processing the request and returning a response.
     *
     * @param array $params Parameters extracted from the request (e.g., query, body, or route parameters).
     * @return mixed The response data, which can be JSON, HTML, or any other format.
     *
     * @example
     * public function handle(array $params): mixed
     * {
     *     return ['status' => 'success', 'data' => $params];
     * }
     */
    public function handle(array $params): mixed;

    /**
     * Get the HTTP method(s) supported by this endpoint.
     *
     * Defines which HTTP methods (e.g., GET, POST, PUT, DELETE) the endpoint can handle.
     * Should return an array of allowed HTTP methods in uppercase.
     *
     * @return array List of supported HTTP methods (e.g., ['GET', 'POST']).
     *
     * @example
     * public function getMethods(): array
     * {
     *     return ['GET', 'POST'];
     * }
     */
    public function getMethods(): array;

    /**
     * Get the route associated with this endpoint.
     *
     * Specifies the URL path this endpoint is responsible for handling.
     * The route should follow RESTful conventions (e.g., "/api/users").
     *
     * @return string The route string (e.g., "/api/products").
     *
     * @example
     * public function getRoute(): string
     * {
     *     return '/api/products';
     * }
     */
    public function getRoute(): string;
}
