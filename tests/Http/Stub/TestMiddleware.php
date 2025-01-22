<?php

namespace MiniCore\Tests\Http\Stub;

use MiniCore\API\MiddlewareInterface;

/**
 * Stub implementation of the MiddlewareInterface for testing purposes.
 *
 * This middleware adds a "middleware" key to the request parameters,
 * simulating basic middleware behavior for testing.
 */
class TestMiddleware implements MiddlewareInterface
{
    /**
     * Handle the middleware logic.
     *
     * @param array $params Request parameters.
     * @param callable $next Next middleware or endpoint handler.
     * @return mixed Processed response.
     */
    public function handle(array $params, callable $next): mixed
    {
        $params['middleware'] = true;
        return $next($params);
    }
}
