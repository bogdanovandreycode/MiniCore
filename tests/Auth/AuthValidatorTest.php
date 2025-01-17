<?php

namespace MiniCore\Tests\Auth;

use PHPUnit\Framework\TestCase;
use MiniCore\Auth\AuthValidator;

/**
 * Unit tests for the AuthValidator class.
 *
 * This test suite verifies the core authentication validation logic, including:
 * - Credential validation with correct and incorrect data.
 * - Password strength validation.
 * - Username format and length validation.
 */
class AuthValidatorTest extends TestCase
{
    /**
     * @var AuthValidator Instance of AuthValidator for testing.
     */
    private AuthValidator $validator;

    /**
     * Initializes the AuthValidator instance before each test.
     */
    protected function setUp(): void
    {
        $this->validator = new AuthValidator();
    }

    /**
     * Tests successful validation of correct user credentials.
     */
    public function testValidateCredentialsSuccess()
    {
        $username = 'admin';
        $password = 'SecurePass123!';

        // Simulates fetching a user from the database.
        $getUserByUsername = function ($name) use ($username, $password) {
            if ($name === $username) {
                return [
                    'username' => $username,
                    'password_hash' => password_hash($password, PASSWORD_BCRYPT)
                ];
            }
            return null;
        };

        $result = $this->validator->validateCredentials($username, $password, $getUserByUsername);
        $this->assertTrue($result);
    }

    /**
     * Tests validation failure when an incorrect password is provided.
     */
    public function testValidateCredentialsWrongPassword()
    {
        $username = 'admin';
        $correctPassword = 'SecurePass123!';
        $wrongPassword = 'WrongPassword';

        $getUserByUsername = function ($name) use ($username, $correctPassword) {
            return [
                'username' => $username,
                'password_hash' => password_hash($correctPassword, PASSWORD_BCRYPT)
            ];
        };

        $result = $this->validator->validateCredentials($username, $wrongPassword, $getUserByUsername);
        $this->assertFalse($result);
    }

    /**
     * Tests validation failure when the user does not exist.
     */
    public function testValidateCredentialsUserNotFound()
    {
        $getUserByUsername = fn($name) => null;

        $result = $this->validator->validateCredentials('unknown', 'password', $getUserByUsername);
        $this->assertFalse($result);
    }

    /**
     * Tests that a strong password passes the strength validation.
     */
    public function testValidatePasswordStrengthSuccess()
    {
        $password = 'StrongPass123!';
        $result = $this->validator->validatePasswordStrength($password);
        $this->assertTrue($result);
    }

    /**
     * Tests that a weak password without special characters fails validation.
     */
    public function testValidatePasswordStrengthWeakPassword()
    {
        $password = 'weakpass';
        $result = $this->validator->validatePasswordStrength($password);
        $this->assertFalse($result);
    }

    /**
     * Tests that a password without digits fails the strength validation.
     */
    public function testValidatePasswordStrengthNoDigits()
    {
        $password = 'StrongPass!';
        $result = $this->validator->validatePasswordStrength($password);
        $this->assertFalse($result);
    }

    /**
     * Tests that a properly formatted username passes validation.
     */
    public function testValidateUsernameSuccess()
    {
        $username = 'user_123';
        $result = $this->validator->validateUsername($username);
        $this->assertTrue($result);
    }

    /**
     * Tests that a username that is too short fails validation.
     */
    public function testValidateUsernameTooShort()
    {
        $username = 'ab';
        $result = $this->validator->validateUsername($username);
        $this->assertFalse($result);
    }

    /**
     * Tests that a username that is too long fails validation.
     */
    public function testValidateUsernameTooLong()
    {
        $username = 'this_username_is_way_too_long';
        $result = $this->validator->validateUsername($username);
        $this->assertFalse($result);
    }

    /**
     * Tests that a username with invalid characters fails validation.
     */
    public function testValidateUsernameInvalidCharacters()
    {
        $username = 'user!@#';
        $result = $this->validator->validateUsername($username);
        $this->assertFalse($result);
    }
}
