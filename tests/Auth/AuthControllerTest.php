<?php

namespace MiniCore\Tests\Auth;

use PHPUnit\Framework\TestCase;
use MiniCore\Auth\AuthController;

/**
 * Unit tests for the AuthController class.
 *
 * This test suite ensures the functionality of the AuthController, including:
 * - User login and session management.
 * - Authentication status checks.
 * - Retrieval of the authenticated user's ID.
 * - User logout functionality.
 *
 * Each test is isolated, and session data is cleared before and after every test.
 */
class AuthControllerTest extends TestCase
{
    /**
     * @var AuthController Instance of the AuthController being tested.
     */
    private AuthController $authController;

    /**
     * @var string The session key used to store the authenticated user ID.
     */
    private string $sessionKey = 'user_id';

    /**
     * Sets up the test environment before each test.
     *
     * Initializes the AuthController and clears any active session to ensure
     * a clean state for testing.
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
     * Tests the login functionality of AuthController.
     *
     * Ensures that the user's ID is correctly stored in the session after login.
     */
    public function testLogin()
    {
        $this->authController->login(42);

        $this->assertArrayHasKey($this->sessionKey, $_SESSION);
        $this->assertEquals(42, $_SESSION[$this->sessionKey]);
    }

    /**
     * Tests the authentication status check.
     *
     * Verifies that the controller correctly identifies whether a user
     * is authenticated based on the session state.
     */
    public function testIsAuthenticated()
    {
        $this->assertFalse($this->authController->isAuthenticated());

        $this->authController->login(42);

        $this->assertTrue($this->authController->isAuthenticated());
    }

    /**
     * Tests retrieving the ID of the authenticated user.
     *
     * Validates that the user ID is correctly returned when a user is logged in
     * and null when no user is authenticated.
     */
    public function testGetUserId()
    {
        $this->assertNull($this->authController->getUserId());

        $this->authController->login(42);

        $this->assertEquals(42, $this->authController->getUserId());
    }

    /**
     * Tests the logout functionality of AuthController.
     *
     * Ensures that the user's session is cleared and the authentication state
     * is reset after logout.
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
     * Cleans up the test environment after each test.
     *
     * Clears any active session and resets the session data to ensure
     * isolation between tests.
     */
    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        $_SESSION = [];
    }
}
