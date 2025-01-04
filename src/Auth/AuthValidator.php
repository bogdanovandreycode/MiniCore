<?php

namespace MiniCore\Auth;

class AuthValidator
{
    /**
     * Validate a user's credentials.
     *
     * @param string $username The username to validate.
     * @param string $password The password to validate.
     * @param callable $getUserByUsername A callback function to fetch user data by username.
     *        The callback should return an associative array with `username` and `password_hash` keys.
     * @return bool True if the credentials are valid, false otherwise.
     */
    public function validateCredentials(string $username, string $password, callable $getUserByUsername): bool
    {
        $user = $getUserByUsername($username);

        if (!$user || !isset($user['password_hash'])) {
            return false; // Пользователь не найден или неверные данные
        }

        return password_verify($password, $user['password_hash']);
    }

    /**
     * Validate the password strength.
     *
     * @param string $password The password to validate.
     * @return bool True if the password meets strength requirements, false otherwise.
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
     * Validate the username format.
     *
     * @param string $username The username to validate.
     * @return bool True if the username is valid, false otherwise.
     */
    public function validateUsername(string $username): bool
    {
        // Имя пользователя должно быть длиной от 3 до 20 символов и содержать только буквы, цифры и символы подчеркивания
        return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username) === 1;
    }
}
