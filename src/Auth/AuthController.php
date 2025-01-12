<?php

namespace MiniCore\Auth;

/**
 * Class AuthController
 *
 * Manages user authentication by handling session-based login and logout.
 * Provides methods to log in users, log them out, check authentication status,
 * and retrieve the authenticated user's ID.
 *
 * @example
 * // Example of using AuthController for user authentication:
 * $authController = new AuthController('user_id');
 *
 * // Log in user with ID 5
 * $authController->login(5);
 *
 * // Check if the user is authenticated
 * if ($authController->isAuthenticated()) {
 *     echo "User ID: " . $authController->getUserId();
 * }
 *
 * // Log out the user
 * $authController->logout();
 */
class AuthController
{
    /**
     * @var string The session key used to store the user ID.
     */
    public function __construct(
        public string $sessionKey = '' // Key for storing the user ID in the session.
    ) {}

    /**
     * Log in the user by setting their ID in the session.
     *
     * Stores the user's ID in the session to authenticate them across requests.
     * Starts a session if one isn't already started.
     *
     * @param int $userId The ID of the authenticated user.
     * @return void
     *
     * @example
     * $authController = new AuthController('user_id');
     * $authController->login(1); // Logs in the user with ID 1
     */
    public function login(int $userId): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION[$this->sessionKey] = $userId;
    }

    /**
     * Log out the user by unsetting their session data.
     *
     * Removes the user's ID from the session, effectively logging them out.
     * Starts a session if one isn't already started.
     *
     * @return void
     *
     * @example
     * $authController = new AuthController('user_id');
     * $authController->logout(); // Logs out the current user
     */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION[$this->sessionKey]);
    }

    /**
     * Check if a user is authenticated.
     *
     * Verifies if the user is currently logged in by checking the session.
     *
     * @return bool True if the user is authenticated, false otherwise.
     *
     * @example
     * $authController = new AuthController('user_id');
     * if ($authController->isAuthenticated()) {
     *     echo "User is logged in.";
     * } else {
     *     echo "User is not logged in.";
     * }
     */
    public function isAuthenticated(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION[$this->sessionKey]);
    }

    /**
     * Get the authenticated user's ID.
     *
     * Retrieves the ID of the currently authenticated user from the session.
     *
     * @return int|null The user ID if authenticated, or null if not authenticated.
     *
     * @example
     * $authController = new AuthController('user_id');
     * $authController->login(42);
     *
     * $userId = $authController->getUserId(); // Returns 42
     * echo $userId ?? "No user is logged in.";
     */
    public function getUserId(): ?int
    {
        if ($this->isAuthenticated()) {
            return $_SESSION[$this->sessionKey];
        }
        return null;
    }
}
