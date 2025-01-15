<?php

namespace Tests\Config\Stub;

use MiniCore\API\EndpointInterface;

/**
 * Заглушка для теста
 */
class TestEndpoint implements EndpointInterface
{
    public function handle(array $params): mixed
    {
        return ['status' => 'success'];
    }

    public function getMethods(): array
    {
        return ['GET'];
    }

    public function getRoute(): string
    {
        return '/api/test';
    }
}
