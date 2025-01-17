<?php

namespace MiniCore\Tests\API;

use PHPUnit\Framework\TestCase;
use MiniCore\Http\Request;
use MiniCore\API\RestEndpoint;

/**
 * Unit tests for the RestEndpoint class.
 *
 * This test suite verifies the correct handling of query and body parameters
 * in the RestEndpoint implementation, including validation of required fields
 * and proper exception handling when required parameters are missing.
 *
 * Covered functionality:
 * - Retrieval of query parameters with and without required fields
 * - Retrieval of body parameters with and without required fields
 * - Exception handling for missing required parameters
 */
class RestEndpointTest extends TestCase
{
    /**
     * @var RestEndpoint Mocked instance of the RestEndpoint for testing purposes.
     */
    private RestEndpoint $endpoint;

    /**
     * Sets up a mock RestEndpoint instance for testing.
     *
     * An anonymous class is used to override abstract methods and simulate
     * endpoint behavior.
     */
    protected function setUp(): void
    {
        // Anonymous class to replace the deprecated getMockForAbstractClass
        $this->endpoint = new class extends RestEndpoint {
            public function handle(array $params): mixed
            {
                return $params;
            }

            public function getMethods(): array
            {
                return ['GET', 'POST'];
            }

            public function getRoute(): string
            {
                return '/api/test';
            }
        };
    }

    /**
     * Tests retrieving query parameters without required fields.
     */
    public function testGetQueryParamsWithoutRequiredArgs()
    {
        $request = new Request('GET', '/api/test', ['role' => 'admin']);

        $result = $this->invokeMethod('getQueryParams', [$request]);

        $this->assertEquals(['role' => 'admin'], $result);
    }

    /**
     * Tests retrieving query parameters with required fields.
     */
    public function testGetQueryParamsWithRequiredArgs()
    {
        $request = new Request('GET', '/api/test', ['role' => 'admin']);

        $result = $this->invokeMethod('getQueryParams', [
            $request,
            [['name' => 'role', 'required' => true]]
        ]);

        $this->assertEquals(['role' => 'admin'], $result);
    }

    /**
     * Tests that an exception is thrown when a required query parameter is missing.
     */
    public function testGetQueryParamsMissingRequiredArg()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Missing required parameter: role');

        $request = new Request('GET', '/api/test', []);

        $this->invokeMethod('getQueryParams', [
            $request,
            [['name' => 'role', 'required' => true]]
        ]);
    }

    /**
     * Tests retrieving body parameters without required fields.
     */
    public function testGetBodyParamsWithoutRequiredArgs()
    {
        $request = new Request('POST', '/api/test', [], ['username' => 'john']);

        $result = $this->invokeMethod('getBodyParams', [$request]);

        $this->assertEquals(['username' => 'john'], $result);
    }

    /**
     * Tests retrieving body parameters with required fields.
     */
    public function testGetBodyParamsWithRequiredArgs()
    {
        $request = new Request('POST', '/api/test', [], ['username' => 'john']);

        $result = $this->invokeMethod('getBodyParams', [
            $request,
            [['name' => 'username', 'required' => true]]
        ]);

        $this->assertEquals(['username' => 'john'], $result);
    }

    /**
     * Tests that an exception is thrown when a required body parameter is missing.
     */
    public function testGetBodyParamsMissingRequiredArg()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Missing required parameter: username');

        $request = new Request('POST', '/api/test', [], []);

        $this->invokeMethod('getBodyParams', [
            $request,
            [['name' => 'username', 'required' => true]]
        ]);
    }

    /**
     * Helper method to invoke protected methods using reflection.
     *
     * @param string $methodName Name of the method to invoke.
     * @param array $parameters Parameters to pass to the method.
     * @return mixed Result of the invoked method.
     */
    private function invokeMethod(string $methodName, array $parameters)
    {
        $reflection = new \ReflectionClass($this->endpoint);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($this->endpoint, $parameters);
    }
}
