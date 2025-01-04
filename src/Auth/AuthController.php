<?php

namespace MiniCore\Auth;

class AuthController
{
    public function __construct(
        public string $sessionKey = '', // Key for storing the user ID in the session.
    ) {}

    /**
     * Log in the user by setting their ID in the session.
     *
     * @param int $userId The ID of the authenticated user.
     * @return void
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
     * @return void
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
     * @return bool True if the user is authenticated, false otherwise.
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
     * @return int|null The user ID, or null if not authenticated.
     */
    public function getUserId(): ?int
    {
        if ($this->isAuthenticated()) {
            return $_SESSION[$this->sessionKey];
        }
        return null;
    }
}
