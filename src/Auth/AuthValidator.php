<?php

namespace MiniCore\Auth;

/**
 * Class AuthValidator
 *
 * Provides authentication-related validation methods for user credentials,
 * password strength, and username format. This class helps ensure that user input
 * meets security and formatting standards.
 */
class AuthValidator
{
    /**
     * Validate a user's credentials.
     *
     * Verifies that the provided username and password match the stored credentials.
     * Uses a callback function to fetch user data from a data source (e.g., database).
     *
     * @param string $username The username provided by the user.
     * @param string $password The password provided by the user.
     * @param callable $getUserByUsername A callback to fetch user data by username.
     *        The callback must return an associative array with `username` and `password_hash` keys.
     * @return bool True if the credentials are valid, false if invalid or the user doesn't exist.
     *
     * @example
     * $authValidator = new AuthValidator();
     * $isValid = $authValidator->validateCredentials('admin', 'password123', function ($username) {
     *     return ['username' => 'admin', 'password_hash' => password_hash('password123', PASSWORD_BCRYPT)];
     * });
     */
    public function validateCredentials(string $username, string $password, callable $getUserByUsername): bool
    {
        $user = $getUserByUsername($username);

        if (!$user || !isset($user['password_hash'])) {
            return false;
        }

        return password_verify($password, $user['password_hash']);
    }

    /**
     * Validate the strength of a password.
     *
     * Ensures the password meets security standards:
     * - Minimum length of 8 characters.
     * - Contains at least one uppercase letter.
     * - Contains at least one lowercase letter.
     * - Contains at least one digit.
     * - Contains at least one special character.
     *
     * @param string $password The password to validate.
     * @return bool True if the password is strong, false otherwise.
     *
     * @example
     * $authValidator = new AuthValidator();
     * $isStrong = $authValidator->validatePasswordStrength('StrongPass123!');
     */
    public function validatePasswordStrength(string $password): bool
    {
        $minLength = 8;
        $hasUppercase = preg_match('/[A-Z]/', $password);
        $hasLowercase = preg_match('/[a-z]/', $password);
        $hasDigit = preg_match('/[0-9]/', $password);
        $hasSpecialChar = preg_match('/[\W_]/', $password);

        return strlen($password) >= $minLength && $hasUppercase && $hasLowercase && $hasDigit && $hasSpecialChar;
    }

    /**
     * Validate the format of a username.
     *
     * Ensures the username:
     * - Is between 3 and 20 characters long.
     * - Contains only letters (a-z, A-Z), digits (0-9), and underscores (_).
     *
     * @param string $username The username to validate.
     * @return bool True if the username is valid, false otherwise.
     *
     * @example
     * $authValidator = new AuthValidator();
     * $isValidUsername = $authValidator->validateUsername('user_123');
     */
    public function validateUsername(string $username): bool
    {
        return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username) === 1;
    }
}
