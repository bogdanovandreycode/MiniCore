<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\Field;
use MiniCore\Form\FieldInterface;

/**
 * Class EmailField
 *
 * Represents a Bootstrap-styled email input field with customizable attributes.
 * This field ensures that the user inputs a valid email format.
 *
 * @package MiniCore\Form\Fields
 *
 * @example
 * // Email input with pre-filled value and placeholder
 * $emailField = new EmailField(
 *     name: 'contact_email',
 *     value: 'user@example.com',
 *     attributes: ['placeholder' => 'Enter your email', 'class' => 'form-control', 'required' => 'required']
 * );
 * echo $emailField->render();
 *
 * // Output:
 * // <input type="email" name="contact_email" value="user@example.com" placeholder="Enter your email" class="form-control" required />
 */
class EmailField extends Field implements FieldInterface
{
    /**
     * EmailField constructor.
     *
     * @param string $name       The name attribute of the email input field.
     * @param mixed  $value      The pre-filled value of the email input.
     * @param array  $attributes Additional HTML attributes for the input field.
     */
    public function __construct(
        string $name = '',
        string $label = '',
        mixed $value = '',
        array $attributes = [],
    ) {
        parent::__construct(
            $name,
            $label,
            $value,
            $attributes
        );
    }

    /**
     * Render the email field as an HTML string.
     *
     * @return string The rendered HTML of the email field.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        return sprintf(
            '<input type="email" name="%s" value="%s" %s/>',
            htmlspecialchars($this->name),
            htmlspecialchars((string)$this->value),
            $attributes
        );
    }
}
