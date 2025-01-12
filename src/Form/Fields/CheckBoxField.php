<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\FieldInterface;

/**
 * Class CheckBoxField
 *
 * Represents a customizable checkbox field for forms.
 * This implementation renders a stylized toggle switch.
 *
 * @package MiniCore\Form\Fields
 *
 * @example
 * // Checkbox with the checked state enabled
 * $checkbox = new CheckBoxField(name: 'subscribe', value: '1', checked: true);
 * echo $checkbox->render();
 *
 * // Output:
 * // <label class="switch">
 * //     <input type="checkbox" name="subscribe" value="1" checked>
 * //     <span class="slider"></span>
 * // </label>
 *
 */
class CheckBoxField implements FieldInterface
{
    /**
     * CheckBoxField constructor.
     *
     * @param string $name       The name attribute of the checkbox.
     * @param mixed  $value      The value attribute of the checkbox.
     * @param bool   $checked    Whether the checkbox should be initially checked.
     * @param array  $attributes Additional HTML attributes for the checkbox input.
     */
    public function __construct(
        public string $name = '',
        public mixed $value = '',
        public bool $checked = false,
        public array $attributes = [],
    ) {}

    /**
     * Render the checkbox field as a custom HTML toggle switch.
     *
     * @return string The rendered custom checkbox HTML.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();
        $checked = $this->checked ? 'checked' : '';

        return sprintf(
            '<label class="switch">
                <input type="checkbox" name="%s" value="%s" %s %s>
                <span class="slider"></span>
            </label>',
            htmlspecialchars($this->name),
            htmlspecialchars((string)$this->value),
            $attributes,
            $checked
        );
    }

    /**
     * Get the name of the checkbox field.
     *
     * @return string The name attribute.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the checkbox field.
     *
     * @return mixed The value attribute.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the checkbox field.
     *
     * @return array The HTML attributes as key-value pairs.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Build the additional attributes into a formatted HTML string.
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
