<?php

namespace MiniCore\API;

use MiniCore\Http\Request;

abstract class RestEndpoint implements EndpointInterface
{
    /**
     * Extract query parameters from the request.
     *
     * @param Request $request
     * @param array $requiredArgs
     * @return array
     * @throws \RuntimeException
     */
    protected function getQueryParams(Request $request, array $requiredArgs = []): array
    {
        $params = $request->getQueryParams();
        return $this->validateParams($params, $requiredArgs);
    }

    /**
     * Extract body parameters from the request.
     *
     * @param Request $request
     * @param array $requiredArgs
     * @return array
     * @throws \RuntimeException
     */
    protected function getBodyParams(Request $request, array $requiredArgs = []): array
    {
        $params = $request->getBodyParams();
        return $this->validateParams($params, $requiredArgs);
    }

    /**
     * Validate parameters against required arguments.
     *
     * @param array $params
     * @param array $requiredArgs
     * @return array
     * @throws \RuntimeException
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
}
