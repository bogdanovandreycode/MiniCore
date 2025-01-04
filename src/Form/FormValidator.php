<?php

namespace Vendor\Undermarket\Core\Form;

class FormValidator
{
    private array $rules = [];
    private array $errors = [];

    /**
     * Add a validation rule for a field.
     *
     * @param string $field The name of the field to validate.
     * @param callable $rule The validation rule as a callable (e.g., function($value) { return true; }).
     * @param string $message The error message if validation fails.
     * @return $this
     */
    public function addRule(string $field, callable $rule, string $message): self
    {
        $this->rules[$field][] = ['rule' => $rule, 'message' => $message];
        return $this;
    }

    /**
     * Validate the given data against the rules.
     *
     * @param array $data The data to validate.
     * @return bool True if validation passes, false otherwise.
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
     * Get the validation errors.
     *
     * @return array The validation errors in the format ['field' => ['error1', 'error2']].
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check if a field has errors.
     *
     * @param string $field The name of the field to check.
     * @return bool True if the field has errors, false otherwise.
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
     */
    public function getFieldErrors(string $field): array
    {
        return $this->errors[$field] ?? [];
    }
}
