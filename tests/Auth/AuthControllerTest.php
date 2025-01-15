<?php

namespace MiniCore\Tests\Auth;

use PHPUnit\Framework\TestCase;
use MiniCore\Auth\AuthController;

class AuthControllerTest extends TestCase
{
    private AuthController $authController;
    private string $sessionKey = 'user_id';

    /**
     * Инициализация перед каждым тестом
     */
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        $_SESSION = [];
        $this->authController = new AuthController($this->sessionKey);
    }

    /**
     * Тест логина пользователя
     */
    public function testLogin()
    {
        $this->authController->login(42);

        $this->assertArrayHasKey($this->sessionKey, $_SESSION);
        $this->assertEquals(42, $_SESSION[$this->sessionKey]);
    }

    /**
     * Тест проверки авторизации пользователя
     */
    public function testIsAuthenticated()
    {
        $this->assertFalse($this->authController->isAuthenticated());

        $this->authController->login(42);

        $this->assertTrue($this->authController->isAuthenticated());
    }

    /**
     * Тест получения ID авторизованного пользователя
     */
    public function testGetUserId()
    {
        $this->assertNull($this->authController->getUserId());

        $this->authController->login(42);

        $this->assertEquals(42, $this->authController->getUserId());
    }

    /**
     * Тест логаута пользователя
     */
    public function testLogout()
    {
        $this->authController->login(42);

        $this->assertTrue($this->authController->isAuthenticated());

        $this->authController->logout();

        $this->assertFalse($this->authController->isAuthenticated());
        $this->assertArrayNotHasKey($this->sessionKey, $_SESSION);
    }

    /**
     * Очистка сессии после тестов
     */
    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        $_SESSION = [];
    }
}
