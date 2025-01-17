<?php

namespace MiniCore\Tests\Form;

use PHPUnit\Framework\TestCase;
use MiniCore\Form\FormValidator;

/**
 * Unit tests for the FormValidator class.
 *
 * This test suite verifies the core functionality of the FormValidator class,
 * ensuring that form data is validated correctly according to specified rules.
 *
 * Covered functionality:
 * - Successful validation of correct data.
 * - Handling validation errors for incorrect data.
 * - Checking for errors on specific fields.
 * - Retrieving detailed error messages.
 * - Applying multiple validation rules to a single field.
 * - Validating data without any rules.
 * - Detecting missing fields in the submitted data.
 */
class FormValidatorTest extends TestCase
{
    /**
     * Tests successful validation without errors.
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

        $this->assertTrue($validator->validate($data), 'Validation should pass with correct data.');
        $this->assertEmpty($validator->getErrors(), 'There should be no validation errors.');
    }

    /**
     * Tests validation failure with incorrect data.
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

        $this->assertFalse($validator->validate($data), 'Validation should fail with incorrect data.');
        $this->assertArrayHasKey('username', $validator->getErrors(), 'Error should exist for username field.');
        $this->assertArrayHasKey('email', $validator->getErrors(), 'Error should exist for email field.');
    }

    /**
     * Tests checking if specific fields have validation errors.
     */
    public function testHasErrors(): void
    {
        $validator = new FormValidator();

        $validator->addRule('password', fn($value) => strlen($value) >= 6, 'Password must be at least 6 characters long');

        $data = ['password' => '123'];

        $validator->validate($data);

        $this->assertTrue($validator->hasErrors('password'), 'Password field should have a validation error.');
        $this->assertFalse($validator->hasErrors('username'), 'Username field should not have a validation error.');
    }

    /**
     * Tests retrieving error messages for specific fields.
     */
    public function testGetFieldErrors(): void
    {
        $validator = new FormValidator();

        $validator->addRule('age', fn($value) => is_numeric($value) && $value >= 18, 'Age must be 18 or older');
        $validator->addRule('age', fn($value) => $value <= 100, 'Age must be 100 or younger');

        $data = ['age' => 15];

        $validator->validate($data);

        $errors = $validator->getFieldErrors('age');

        $this->assertCount(1, $errors, 'There should be one error for the age field.');
        $this->assertEquals('Age must be 18 or older', $errors[0], 'Error message should match the rule.');
    }

    /**
     * Tests applying multiple rules to a single field.
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

        $this->assertCount(2, $errors, 'Password field should have two validation errors.');
        $this->assertContains('Password must contain an uppercase letter', $errors, 'Error about uppercase letter should be present.');
        $this->assertContains('Password must contain a number', $errors, 'Error about number should be present.');
    }

    /**
     * Tests validation without any defined rules.
     */
    public function testValidationWithoutRules(): void
    {
        $validator = new FormValidator();

        $data = ['any_field' => 'any_value'];

        $this->assertTrue($validator->validate($data), 'Validation should pass if no rules are defined.');
        $this->assertEmpty($validator->getErrors(), 'There should be no validation errors.');
    }

    /**
     * Tests validation when a required field is missing.
     */
    public function testMissingFieldInData(): void
    {
        $validator = new FormValidator();

        $validator->addRule('email', fn($value) => !empty($value), 'Email is required');

        $data = [];

        $validator->validate($data);

        $this->assertTrue($validator->hasErrors('email'), 'Error should exist for missing email field.');
        $this->assertEquals(['Email is required'], $validator->getFieldErrors('email'), 'Error message should be correct.');
    }
}
