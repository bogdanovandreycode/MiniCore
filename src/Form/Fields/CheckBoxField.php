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
 * $checkbox = new CheckBoxField(name: 'subscribe', value: '1', checked: true);
 * echo $checkbox->render();
 * 
 * // Output:
 * <div class="form-check form-switch">
 *     <input type="checkbox" name="subscribe" value="1" class="form-check-input" checked>
 *     <label class="form-check-label">Subscribe</label>
 * </div>
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
     * Render the checkbox field as a Bootstrap-styled toggle switch.
     *
     * This method generates a checkbox field using Bootstrap's `form-switch` class 
     * to render it as a toggle switch. If no custom classes are provided, it applies 
     * the necessary Bootstrap classes for consistent styling.
     *
     * @return string The rendered HTML for the checkbox field.
     *
     */
    public function render(): string
    {
        if (!isset($this->attributesattributes['class']) || !str_contains($this->attributes['class'], 'form-check-input')) {
            $this->attributes['class'] = trim(($this->attributes['class'] ?? '') . ' form-check-input');
        }

        $attributesString = $this->buildAttributes();
        $checked = $this->checked ? 'checked' : '';

        return sprintf(
            '<div class="form-check form-switch">
            <input type="checkbox" name="%s" value="%s" %s %s>
            <label class="form-check-label" for="%s">%s</label>
        </div>',
            htmlspecialchars($this->name),
            htmlspecialchars((string)$this->value),
            $attributesString,
            $checked,
            htmlspecialchars($this->name),
            ucfirst(htmlspecialchars($this->name))
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
