<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\FieldInterface;

/**
 * Class MultiSelectField
 *
 * Represents a customizable multi-select input field using checkboxes.
 * This field allows users to select multiple options from a given list.
 *
 * @package MiniCore\Form\Fields
 *
 * @example
 * // Multi-select field with additional CSS classes and attributes
 * $multiSelect = new MultiSelectField(
 *     name: 'hobbies',
 *     options: [
 *         'reading' => 'Reading',
 *         'traveling' => 'Traveling',
 *         'gaming' => 'Gaming'
 *     ],
 *     selected: ['gaming'],
 *     attributes: ['class' => 'custom-multi-select', 'data-type' => 'hobby']
 * );
 * echo $multiSelect->render();
 *
 * // Output:
 * // <div class="multi-select-field custom-multi-select" data-type="hobby">
 * //     <label class="multi-select-option">
 * //         <input type="checkbox" name="hobbies[]" value="reading">
 * //         <span class="option-label">Reading</span>
 * //     </label>
 * //     <label class="multi-select-option">
 * //         <input type="checkbox" name="hobbies[]" value="traveling">
 * //         <span class="option-label">Traveling</span>
 * //     </label>
 * //     <label class="multi-select-option">
 * //         <input type="checkbox" name="hobbies[]" value="gaming" checked>
 * //         <span class="option-label">Gaming</span>
 * //     </label>
 * // </div>
 */
class MultiSelectField implements FieldInterface
{
    /**
     * MultiSelectField constructor.
     *
     * @param string $name       The name attribute of the multi-select field.
     * @param array  $options    An associative array of value => label pairs.
     * @param array  $selected   An array of selected option values.
     * @param array  $attributes Additional HTML attributes for the field.
     */
    public function __construct(
        public string $name = '',
        public array $options = [],
        public array $selected = [],
        public array $attributes = [],
    ) {}

    /**
     * Render the multi-select field as a custom HTML string with checkboxes.
     *
     * @return string The rendered custom multi-select HTML.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        $optionsHtml = '';
        foreach ($this->options as $value => $label) {
            $checked = in_array($value, $this->selected, true) ? 'checked' : '';
            $optionsHtml .= sprintf(
                '<label class="multi-select-option">
                    <input type="checkbox" name="%s[]" value="%s" %s>
                    <span class="option-label">%s</span>
                </label>',
                htmlspecialchars($this->name),
                htmlspecialchars((string)$value),
                $checked,
                htmlspecialchars($label)
            );
        }

        return sprintf(
            '<div class="multi-select-field" %s>%s</div>',
            $attributes,
            $optionsHtml
        );
    }

    /**
     * Get the name of the multi-select field.
     *
     * @return string The name attribute.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the selected values of the multi-select field.
     *
     * @return array The selected values.
     */
    public function getValue(): mixed
    {
        return $this->selected;
    }

    /**
     * Get the additional attributes of the multi-select field.
     *
     * @return array The HTML attributes.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Build the attributes as an HTML string.
     *
     * @return string The HTML attributes.
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
