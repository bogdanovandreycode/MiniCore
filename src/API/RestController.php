<?php

namespace Vendor\Undermarket\Core\API;

use Vendor\Undermarket\Core\Http\Request;
use Vendor\Undermarket\Core\Http\Response;

abstract class RestController
{
    /** @var array List of registered endpoints */
    protected array $endpoints = [];

    /**
     * Register an endpoint.
     *
     * @param EndpointInterface $endpoint The endpoint to register.
     */
    public function registerEndpoint(EndpointInterface $endpoint): void
    {
        $this->endpoints[$endpoint->getRoute()] = $endpoint;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request The incoming HTTP request.
     * @return Response The HTTP response.
     */
    public function handleRequest(Request $request): Response
    {
        $route = $request->getPath();
        $method = $request->getMethod();

        if (!isset($this->endpoints[$route])) {
            return new Response(404, ['error' => 'Endpoint not found']);
        }

        $endpoint = $this->endpoints[$route];

        if (!in_array($method, $endpoint->getMethods(), true)) {
            return new Response(405, ['error' => 'Method not allowed']);
        }

        try {
            $params = $request->getParams();
            $result = $endpoint->handle($params);
            return new Response(200, $result);
        } catch (\Exception $e) {
            return new Response(500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get all registered endpoints.
     *
     * @return array List of registered endpoints.
     */
    public function getRegisteredEndpoints(): array
    {
        return $this->endpoints;
    }
}
