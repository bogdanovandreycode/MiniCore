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
}
