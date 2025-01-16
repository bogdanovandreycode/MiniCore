<?php

namespace MiniCore\Tests\Form;

use PHPUnit\Framework\TestCase;
use MiniCore\Form\FormValidator;

/**
 * Class FormValidatorTest
 *
 * Unit tests for the FormValidator class.
 */
class FormValidatorTest extends TestCase
{
    /**
     * Проверка успешной валидации без ошибок.
     */
    public function testValidationSuccess(): void
    {
        $validator = new FormValidator();

        $validator->addRule('username', fn($value) => !empty($value), 'Username is required');
        $validator->addRule('email', fn($value) => filter_var($value, FILTER_VALIDATE_EMAIL), 'Invalid email address');

        $data = [
            'username' => 'JohnDoe',
            'email' => 'john@example.com'
        ];

        $this->assertTrue($validator->validate($data), 'Валидация должна пройти успешно при корректных данных.');
        $this->assertEmpty($validator->getErrors(), 'Ошибок быть не должно.');
    }

    /**
     * Проверка валидации с ошибками.
     */
    public function testValidationFailure(): void
    {
        $validator = new FormValidator();

        $validator->addRule('username', fn($value) => !empty($value), 'Username is required');
        $validator->addRule('email', fn($value) => filter_var($value, FILTER_VALIDATE_EMAIL), 'Invalid email address');

        $data = [
            'username' => '',
            'email' => 'invalid-email'
        ];

        $this->assertFalse($validator->validate($data), 'Валидация должна завершиться ошибкой.');
        $this->assertArrayHasKey('username', $validator->getErrors(), 'Ошибка должна быть для поля username.');
        $this->assertArrayHasKey('email', $validator->getErrors(), 'Ошибка должна быть для поля email.');
    }

    /**
     * Проверка наличия ошибок у конкретного поля.
     */
    public function testHasErrors(): void
    {
        $validator = new FormValidator();

        $validator->addRule('password', fn($value) => strlen($value) >= 6, 'Password must be at least 6 characters long');

        $data = ['password' => '123'];

        $validator->validate($data);

        $this->assertTrue($validator->hasErrors('password'), 'Поле password должно содержать ошибку.');
        $this->assertFalse($validator->hasErrors('username'), 'Поле username не должно содержать ошибок.');
    }

    /**
     * Проверка получения сообщений об ошибках для конкретного поля.
     */
    public function testGetFieldErrors(): void
    {
        $validator = new FormValidator();

        $validator->addRule('age', fn($value) => is_numeric($value) && $value >= 18, 'Age must be 18 or older');
        $validator->addRule('age', fn($value) => $value <= 100, 'Age must be 100 or younger');

        $data = ['age' => 15];

        $validator->validate($data);

        $errors = $validator->getFieldErrors('age');

        $this->assertCount(1, $errors, 'Должна быть одна ошибка для поля age.');
        $this->assertEquals('Age must be 18 or older', $errors[0], 'Ошибка должна соответствовать правилу.');
    }

    /**
     * Проверка работы нескольких правил для одного поля.
     */
    public function testMultipleRulesForField(): void
    {
        $validator = new FormValidator();

        $validator->addRule('password', fn($value) => strlen($value) >= 6, 'Password must be at least 6 characters');
        $validator->addRule('password', fn($value) => preg_match('/[A-Z]/', $value), 'Password must contain an uppercase letter');
        $validator->addRule('password', fn($value) => preg_match('/[0-9]/', $value), 'Password must contain a number');

        $data = ['password' => 'abcdef'];

        $validator->validate($data);

        $errors = $validator->getFieldErrors('password');

        $this->assertCount(2, $errors, 'Должно быть две ошибки для поля password.');
        $this->assertContains('Password must contain an uppercase letter', $errors, 'Ошибка про заглавную букву должна быть.');
        $this->assertContains('Password must contain a number', $errors, 'Ошибка про цифру должна быть.');
    }


    /**
     * Проверка валидации без добавленных правил.
     */
    public function testValidationWithoutRules(): void
    {
        $validator = new FormValidator();

        $data = ['any_field' => 'any_value'];

        $this->assertTrue($validator->validate($data), 'Валидация должна пройти успешно, если нет правил.');
        $this->assertEmpty($validator->getErrors(), 'Ошибок быть не должно.');
    }

    /**
     * Проверка валидации отсутствующего поля в данных.
     */
    public function testMissingFieldInData(): void
    {
        $validator = new FormValidator();

        $validator->addRule('email', fn($value) => !empty($value), 'Email is required');

        $data = []; // Поле email отсутствует

        $validator->validate($data);

        $this->assertTrue($validator->hasErrors('email'), 'Ошибка должна быть для отсутствующего поля email.');
        $this->assertEquals(['Email is required'], $validator->getFieldErrors('email'), 'Сообщение об ошибке должно быть корректным.');
    }
}
