<?php

namespace MiniCore\Form;

/**
 * Interface FieldInterface
 *
 * Defines the basic structure for all form fields in the form builder system.
 * Any custom form field must implement this interface to ensure consistency.
 *
 * @package MiniCore\Form
 */
interface FieldInterface
{
    /**
     * Render the field as an HTML string.
     *
     * This method is responsible for generating the HTML output of the form field.
     * It should return the complete HTML tag(s) for the field, including its attributes and value.
     *
     * @return string The rendered HTML of the field.
     *
     * @example
     * // For an input field:
     * echo $field->render();
     * // Output: <input type="text" name="username" value="JohnDoe">
     */
    public function render(): string;

    /**
     * Get the name of the field.
     *
     * Returns the `name` attribute of the form field, which is used when submitting form data.
     *
     * @return string The name attribute of the field.
     *
     * @example
     * $field->getName(); // Output: "username"
     */
    public function getName(): string;

    /**
     * Get the value of the field.
     *
     * Retrieves the current value of the field, which could be a default value or user input.
     *
     * @return mixed The value of the field.
     *
     * @example
     * $field->getValue(); // Output: "JohnDoe"
     */
    public function getValue(): mixed;

    /**
     * Get the additional attributes of the field.
     *
     * Returns any extra HTML attributes set for the field (e.g., `class`, `id`, `placeholder`).
     *
     * @return array The attributes as key-value pairs.
     *
     * @example
     * $field->getAttributes();
     * // Output: ['class' => 'input-field', 'placeholder' => 'Enter your name']
     */
    public function getAttributes(): array;

    /**
     * Build the additional attributes of the field as an HTML string.
     *
     * Converts the attributes array into a properly formatted HTML string.
     * Useful for embedding attributes directly into the HTML output.
     *
     * @return string The attributes as an HTML string.
     *
     * @example
     * $field->buildAttributes();
     * // Output: 'class="input-field" placeholder="Enter your name"'
     */
    public function buildAttributes(): string;
}
