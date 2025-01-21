<?php

namespace MiniCore\API;

/**
 * Interface MiddlewareInterface
 *
 * Defines the contract for middleware classes.
 */
interface MiddlewareInterface
{
    /**
     * Handle the middleware logic.
     *
     * @param array $params Request parameters.
     * @param callable $next Next middleware or endpoint handler.
     * @return mixed
     */
    public function handle(array $params, callable $next): mixed;
}
