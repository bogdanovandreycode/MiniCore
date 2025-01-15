<?php

namespace Tests\Config;

use PHPUnit\Framework\TestCase;
use MiniCore\Config\Env;

class EnvTest extends TestCase
{
    private string $envPath;

    /**
     * Подготовка тестов
     */
    protected function setUp(): void
    {
        $this->envPath = __DIR__ . '/Data/';
    }

    /**
     * Тест успешной загрузки .env файла
     */
    public function testLoadEnvFile()
    {
        Env::load($this->envPath, '/.env.test');

        $this->assertEquals('127.0.0.1', Env::get('DB_HOST'));
        $this->assertEquals('test_user', Env::get('DB_USER'));
        $this->assertEquals('true', Env::get('APP_DEBUG'));
    }

    /**
     * Тест получения переменной с дефолтным значением
     */
    public function testGetEnvVariableWithDefault()
    {
        $this->assertEquals('default_value', Env::get('NON_EXISTENT_KEY', 'default_value'));
    }

    /**
     * Тест установки переменной в окружение
     */
    public function testSetEnvVariable()
    {
        Env::set('NEW_VAR', 'new_value');

        $this->assertEquals('new_value', Env::get('NEW_VAR'));
        $this->assertEquals('new_value', getenv('NEW_VAR'));
    }

    /**
     * Тест ошибки при отсутствии .env файла
     */
    public function testLoadNonExistentEnvFile()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Environment file not found');

        Env::load($this->envPath, '.env.nonexistent');
    }

    /**
     * Очистка переменных окружения после тестов
     */
    protected function tearDown(): void
    {
        unset($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['APP_DEBUG'], $_ENV['NEW_VAR']);
        putenv('NEW_VAR');
    }
}
