<?php

namespace MiniCore\API;

use MiniCore\Http\Request;

/**
 * Class RestEndpoint
 *
 * Provides a base implementation for REST API endpoints.
 * This abstract class simplifies working with query and body parameters
 * and ensures required parameters are properly validated.
 *
 * Any custom endpoint should extend this class to automatically handle
 * common request processing tasks.
 *
 * @example
 * // Example of extending RestEndpoint to create a GET endpoint:
 * use MiniCore\Http\Request;
 * use MiniCore\API\RestEndpoint;
 *
 * class GetUserEndpoint extends RestEndpoint
 * {
 *     public function handle(array $params): mixed
 *     {
 *         // Simulate fetching user data
 *         return ['id' => 1, 'name' => 'John Doe'];
 *     }
 *
 *     public function getMethods(): array
 *     {
 *         return ['GET'];
 *     }
 *
 *     public function getRoute(): string
 *     {
 *         return '/api/user';
 *     }
 * }
 */
abstract class RestEndpoint
{
    /**
     * List of middleware classes to apply to the endpoint.
     *
     * @var array
     */
    protected array $middlewares = [];

    /**
     * Extract query parameters from the request.
     *
     * Retrieves query parameters (e.g., from a URL like `/api/users?role=admin`)
     * and validates required arguments.
     *
     * @param Request $request The HTTP request object.
     * @param array $requiredArgs List of required arguments (e.g., [['name' => 'role', 'required' => true]]).
     * @return array The validated query parameters.
     * @throws \RuntimeException If a required parameter is missing.
     *
     * @example
     * $params = $this->getQueryParams($request, [
     *     ['name' => 'role', 'required' => true],
     * ]);
     */
    protected function getQueryParams(Request $request, array $requiredArgs = []): array
    {
        $params = $request->getQueryParams();
        return $this->validateParams($params, $requiredArgs);
    }

    /**
     * Extract body parameters from the request.
     *
     * Retrieves body parameters (typically for POST, PUT, or PATCH requests)
     * and validates required arguments.
     *
     * @param Request $request The HTTP request object.
     * @param array $requiredArgs List of required arguments (e.g., [['name' => 'username', 'required' => true]]).
     * @return array The validated body parameters.
     * @throws \RuntimeException If a required parameter is missing.
     *
     * @example
     * $params = $this->getBodyParams($request, [
     *     ['name' => 'username', 'required' => true],
     *     ['name' => 'email', 'required' => true],
     * ]);
     */
    protected function getBodyParams(Request $request, array $requiredArgs = []): array
    {
        $params = $request->getBodyParams();
        return $this->validateParams($params, $requiredArgs);
    }

    /**
     * Validate parameters against required arguments.
     *
     * Checks if all required parameters are present in the request.
     * Throws an exception if any required parameter is missing.
     *
     * @param array $params The parameters to validate (from query or body).
     * @param array $requiredArgs List of required arguments with `name` and `required` flags.
     * @return array The validated parameters.
     * @throws \RuntimeException If any required parameter is missing.
     *
     * @example
     * // Example usage in a custom method:
     * $validatedParams = $this->validateParams($params, [
     *     ['name' => 'id', 'required' => true],
     *     ['name' => 'status', 'required' => false],
     * ]);
     */
    private function validateParams(array $params, array $requiredArgs): array
    {
        foreach ($requiredArgs as $arg) {
            if ($arg['required'] && !isset($params[$arg['name']])) {
                throw new \RuntimeException("Missing required parameter: {$arg['name']}");
            }
        }

        return $params;
    }

    /**
     * Register middleware for the endpoint.
     *
     * @param MiddlewareInterface $middleware Middleware instance.
     * @return void
     */
    public function addMiddleware(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * Process the middleware chain and execute the endpoint's core logic.
     *
     * @param array $params Parameters for the endpoint.
     * @return mixed The response from the endpoint.
     */
    public function process(array $params): mixed
    {
        // Create a callable for the final handler (main logic of the endpoint).
        $handler = function (array $params) {
            return $this->handle($params);
        };

        // Wrap the handler with middleware in reverse order.
        foreach (array_reverse($this->middlewares) as $middleware) {
            $handler = function (array $params) use ($middleware, $handler) {
                return $middleware->handle($params, $handler);
            };
        }

        // Execute the final wrapped handler.
        return $handler($params);
    }

    /**
     * Abstract method to handle the endpoint logic.
     *
     * @param array $params
     * @return mixed
     */
    abstract public function handle(array $params): mixed;
}
