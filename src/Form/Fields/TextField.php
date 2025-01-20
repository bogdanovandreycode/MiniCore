<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\FieldInterface;

/**
 * Class TextField
 *
 * Represents a Bootstrap-styled HTML `<input type="text">` field.
 * This class allows the creation of customizable text input fields for forms with
 * support for additional attributes like placeholder, maxlength, CSS classes, etc.
 *
 * @package MiniCore\Form\Fields
 *
 * @example
 * // Text input with additional attributes
 * $textField = new TextField(
 *     name: 'search',
 *     value: '',
 *     attributes: [
 *         'placeholder' => 'Search...',
 *         'class' => 'form-control',
 *         'maxlength' => '50'
 *     ]
 * );
 * echo $textField->render();
 *
 * // Output:
 * // <input type="text" name="search" value="" placeholder="Search..." class="form-control" maxlength="50"/>
 */
class TextField implements FieldInterface
{
    /**
     * TextField constructor.
     *
     * @param string $name       The name attribute of the text input.
     * @param mixed  $value      The default value of the text input.
     * @param array  $attributes Additional HTML attributes for customization (e.g., placeholder, maxlength, class).
     */
    public function __construct(
        public string $name = '',
        public mixed $value = '',
        public array $attributes = [],
    ) {}

    /**
     * Render the text field as an HTML string.
     *
     * @return string The rendered HTML of the text field.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        return sprintf(
            '<input type="text" name="%s" value="%s" class="form-control" %s/>',
            htmlspecialchars($this->name),
            htmlspecialchars((string)$this->value),
            $attributes
        );
    }

    /**
     * Get the name of the text field.
     *
     * @return string The name attribute.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the text field.
     *
     * @return mixed The value attribute.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the text field.
     *
     * @return array The HTML attributes as key-value pairs.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Build the attributes as an HTML string.
     *
     * @return string The formatted HTML attributes for rendering.
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
