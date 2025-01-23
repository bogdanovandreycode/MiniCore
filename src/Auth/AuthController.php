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
 * if(!AuthController::isSessionKey()) {
 *    AuthController::setSessionKey('user_id');
 * }
 * 
 * // Log in user with ID 5
 * AuthController::login(5);
 *
 * // Check if the user is authenticated
 * if (AuthController::isAuthenticated()) {
 *     echo "User ID: " . AuthController::getUserId();
 * }
 *
 * // Log out the user
 * AuthController::logout();
 */
class AuthController
{
    /**
     * @var string The session key used to store the user ID.
     */
    private static string $sessionKey = '';

    /**
     * Setter for session key propery.
     * @param string $sessionKey The ID of the authenticated user.
     * @return void
     *
     * @example
     * AuthController::setSessionKey('user_id');
     */
    public static function setSessionKey(string $sessionKey): void
    {
        self::$sessionKey = $sessionKey;
    }

    /**
     * Getter for session key propery.
     * @return string $sessionKey The ID of the authenticated user.
     *
     * @example
     * $userId = AuthController::getSessionKey();
     */
    public static function getSessionKey(): string
    {
        return self::$sessionKey;
    }

    /**
     * Check if the session key exists.
     * @return bool
     *
     * @example
     * AuthController::isSessionKey();
     */
    public static function isSessionKey(): bool
    {
        return !empty(self::$sessionKey);
    }

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
    public static function login(int $userId): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION[self::$sessionKey] = $userId;
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
    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION[self::$sessionKey]);
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
    public static function isAuthenticated(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION[self::$sessionKey]);
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
    public static function getUserId(): ?int
    {
        if (self::isAuthenticated()) {
            return $_SESSION[self::$sessionKey];
        }

        return null;
    }
}
