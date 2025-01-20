<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\Field;
use MiniCore\Form\FieldInterface;

/**
 * Class PasswordField
 *
 * Represents a Bootstrap-styled password input field in a form.
 * This field hides user input by masking characters for security.
 * Additional HTML attributes can be passed for customization (e.g., placeholder, CSS classes).
 *
 * @package MiniCore\Form\Fields
 *
 * @example
 * // Password field with placeholder and Bootstrap class
 * $passwordField = new PasswordField(
 *     name: 'admin_password',
 *     attributes: ['placeholder' => 'Enter your password', 'class' => 'form-control']
 * );
 * echo $passwordField->render();
 *
 * // Output:
 * // <input type="password" name="admin_password" value="" placeholder="Enter your password" class="form-control"/>
 */
class PasswordField extends Field implements FieldInterface
{
    /**
     * PasswordField constructor.
     *
     * @param string $name       The name attribute of the password input field.
     * @param mixed  $value      The value of the password field (rarely used for security reasons).
     * @param array  $attributes Additional HTML attributes (e.g., placeholder, class, id).
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
     * Render the password field as an HTML string.
     *
     * @return string The rendered HTML of the password field.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        return sprintf(
            '<input type="password" name="%s" value="%s" %s/>',
            htmlspecialchars($this->name),
            htmlspecialchars((string)$this->value),
            $attributes
        );
    }
}
