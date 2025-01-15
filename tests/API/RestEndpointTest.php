<?php

namespace MiniCore\Tests\API;

use PHPUnit\Framework\TestCase;
use MiniCore\Http\Request;
use MiniCore\API\RestEndpoint;

/**
 * Тест RestEndpoint
 */
class RestEndpointTest extends TestCase
{
    /**
     * @var RestEndpoint
     */
    private RestEndpoint $endpoint;

    /**
     * Создание заглушки для RestEndpoint
     */
    protected function setUp(): void
    {
        // Анонимный класс для замены устаревшего getMockForAbstractClass
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
     * Тест получения query параметров без обязательных полей
     */
    public function testGetQueryParamsWithoutRequiredArgs()
    {
        $request = new Request('GET', '/api/test', ['role' => 'admin']);

        $result = $this->invokeMethod('getQueryParams', [$request]);

        $this->assertEquals(['role' => 'admin'], $result);
    }

    /**
     * Тест получения query параметров с обязательными полями
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
     * Тест исключения при отсутствии обязательного query параметра
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
     * Тест получения body параметров без обязательных полей
     */
    public function testGetBodyParamsWithoutRequiredArgs()
    {
        $request = new Request('POST', '/api/test', [], ['username' => 'john']);

        $result = $this->invokeMethod('getBodyParams', [$request]);

        $this->assertEquals(['username' => 'john'], $result);
    }

    /**
     * Тест получения body параметров с обязательными полями
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
     * Тест исключения при отсутствии обязательного body параметра
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
     * Вспомогательный метод для вызова защищённых методов
     */
    private function invokeMethod(string $methodName, array $parameters)
    {
        $reflection = new \ReflectionClass($this->endpoint);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($this->endpoint, $parameters);
    }
}
