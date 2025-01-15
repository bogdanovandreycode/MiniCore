<?php

namespace MiniCore\Tests\Auth;

use PHPUnit\Framework\TestCase;
use MiniCore\Auth\AuthValidator;

class AuthValidatorTest extends TestCase
{
    private AuthValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new AuthValidator();
    }

    /**
     * Тест проверки валидных учетных данных
     */
    public function testValidateCredentialsSuccess()
    {
        $username = 'admin';
        $password = 'SecurePass123!';

        // Симуляция получения пользователя из базы данных
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
     * Тест проверки неверного пароля
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
     * Тест проверки несуществующего пользователя
     */
    public function testValidateCredentialsUserNotFound()
    {
        $getUserByUsername = fn($name) => null;

        $result = $this->validator->validateCredentials('unknown', 'password', $getUserByUsername);
        $this->assertFalse($result);
    }

    /**
     * Тест валидного сложного пароля
     */
    public function testValidatePasswordStrengthSuccess()
    {
        $password = 'StrongPass123!';
        $result = $this->validator->validatePasswordStrength($password);
        $this->assertTrue($result);
    }

    /**
     * Тест простого пароля (без спецсимволов)
     */
    public function testValidatePasswordStrengthWeakPassword()
    {
        $password = 'weakpass';
        $result = $this->validator->validatePasswordStrength($password);
        $this->assertFalse($result);
    }

    /**
     * Тест сложного пароля, не содержащего цифры
     */
    public function testValidatePasswordStrengthNoDigits()
    {
        $password = 'StrongPass!';
        $result = $this->validator->validatePasswordStrength($password);
        $this->assertFalse($result);
    }

    /**
     * Тест валидного имени пользователя
     */
    public function testValidateUsernameSuccess()
    {
        $username = 'user_123';
        $result = $this->validator->validateUsername($username);
        $this->assertTrue($result);
    }

    /**
     * Тест слишком короткого имени пользователя
     */
    public function testValidateUsernameTooShort()
    {
        $username = 'ab';
        $result = $this->validator->validateUsername($username);
        $this->assertFalse($result);
    }

    /**
     * Тест слишком длинного имени пользователя
     */
    public function testValidateUsernameTooLong()
    {
        $username = 'this_username_is_way_too_long';
        $result = $this->validator->validateUsername($username);
        $this->assertFalse($result);
    }

    /**
     * Тест имени пользователя с недопустимыми символами
     */
    public function testValidateUsernameInvalidCharacters()
    {
        $username = 'user!@#';
        $result = $this->validator->validateUsername($username);
        $this->assertFalse($result);
    }
}
