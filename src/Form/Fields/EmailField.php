<?php

namespace MiniCore\Form\Fields;

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
class EmailField implements FieldInterface
{
    /**
     * EmailField constructor.
     *
     * @param string $name       The name attribute of the email input field.
     * @param mixed  $value      The pre-filled value of the email input.
     * @param array  $attributes Additional HTML attributes for the input field.
     */
    public function __construct(
        public string $name = '',
        public mixed $value = '',
        public array $attributes = [],
    ) {}

    /**
     * Render the email field as an HTML string.
     *
     * @return string The rendered HTML of the email field.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        return sprintf(
            '<input type="email" name="%s" value="%s" class="form-control" %s/>',
            htmlspecialchars($this->name),
            htmlspecialchars((string)$this->value),
            $attributes
        );
    }

    /**
     * Get the name of the email field.
     *
     * @return string The name attribute.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the email field.
     *
     * @return mixed The value of the input field.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the email field.
     *
     * @return array The key-value pairs of attributes.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Build the attributes as an HTML string.
     *
     * @return string The formatted HTML attributes.
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
