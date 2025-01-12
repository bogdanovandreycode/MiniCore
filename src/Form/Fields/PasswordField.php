<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\FieldInterface;

/**
 * Class PasswordField
 *
 * Represents a secure password input field in a form.
 * This field hides user input by masking characters for security.
 * Additional HTML attributes can be passed for customization (e.g., placeholder, CSS classes).
 *
 * @package MiniCore\Form\Fields
 *
 * @example
 * // Password field with placeholder and custom CSS class
 * $passwordField = new PasswordField(
 *     name: 'admin_password',
 *     attributes: ['placeholder' => 'Enter your password', 'class' => 'password-input']
 * );
 * echo $passwordField->render();
 *
 * // Output:
 * // <input type="password" name="admin_password" value="" placeholder="Enter your password" class="password-input"/>
 */
class PasswordField implements FieldInterface
{
    /**
     * PasswordField constructor.
     *
     * @param string $name       The name attribute of the password input field.
     * @param mixed  $value      The value of the password field (rarely used for security reasons).
     * @param array  $attributes Additional HTML attributes (e.g., placeholder, class, id).
     */
    public function __construct(
        public string $name = '',
        public mixed $value = '',
        public array $attributes = [],
    ) {}

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

    /**
     * Get the name of the password field.
     *
     * @return string The name attribute.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the password field.
     *
     * @return mixed The value attribute.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the password field.
     *
     * @return array The HTML attributes as key-value pairs.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Build the additional attributes into an HTML string.
     *
     * @return string The compiled HTML attributes.
     */
    public function buildAttributes(): string
    {
        $result = '';

        foreach ($this->attributes as $key => $value) {
            $result .= sprintf('%s="%s" ', htmlspecialchars($key), htmlspecialchars($value));
        }

        return trim($result);
    }
}
