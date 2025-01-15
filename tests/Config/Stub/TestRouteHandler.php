<?php

namespace MiniCore\Tests\Config\Stub;

use MiniCore\API\EndpointInterface;

class TestRouteHandler implements EndpointInterface
{
    public function handle(array $params): mixed
    {
        return ['status' => 'route success'];
    }

    public function getMethods(): array
    {
        return ['GET'];
    }

    public function getRoute(): string
    {
        return '/api/example';
    }
}
