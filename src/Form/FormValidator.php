<?php

namespace MiniCore\Form;

/**
 * Class FormValidator
 *
 * Provides a flexible and extensible way to validate form data.
 * You can define custom validation rules for each form field and handle validation errors.
 *
 * @package MiniCore\Form
 */
class FormValidator
{
    /**
     * @var array List of validation rules for each field.
     */
    private array $rules = [];

    /**
     * @var array List of validation errors after validation.
     */
    private array $errors = [];

    /**
     * Add a validation rule for a specific form field.
     *
     * @param string $field The name of the field to validate.
     * @param callable $rule The validation rule as a callable (e.g., function($value) { return true; }).
     * @param string $message The error message if validation fails.
     * @return $this
     *
     * @example
     * $validator->addRule('email', fn($value) => filter_var($value, FILTER_VALIDATE_EMAIL), 'Invalid email address');
     */
    public function addRule(string $field, callable $rule, string $message): self
    {
        $this->rules[$field][] = ['rule' => $rule, 'message' => $message];
        return $this;
    }

    /**
     * Validate the given data against the defined rules.
     *
     * @param array $data The associative array of data to validate (e.g., $_POST).
     * @return bool True if all validations pass, false if there are errors.
     *
     * @example
     * $isValid = $validator->validate($_POST);
     */
    public function validate(array $data): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                if (!call_user_func($rule['rule'], $value)) {
                    $this->errors[$field][] = $rule['message'];
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * Get all validation errors.
     *
     * @return array The validation errors in the format ['field' => ['error1', 'error2']].
     *
     * @example
     * print_r($validator->getErrors());
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check if a specific field has validation errors.
     *
     * @param string $field The name of the field to check.
     * @return bool True if the field has errors, false otherwise.
     *
     * @example
     * if ($validator->hasErrors('email')) { echo "Invalid email"; }
     */
    public function hasErrors(string $field): bool
    {
        return !empty($this->errors[$field]);
    }

    /**
     * Get the error messages for a specific field.
     *
     * @param string $field The name of the field.
     * @return array The error messages for the field.
     *
     * @example
     * print_r($validator->getFieldErrors('username'));
     */
    public function getFieldErrors(string $field): array
    {
        return $this->errors[$field] ?? [];
    }
}
