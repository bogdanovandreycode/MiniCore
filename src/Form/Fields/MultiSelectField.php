<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\FieldInterface;

/**
 * Class MultiSelectField
 *
 * Represents a Bootstrap-styled multi-select input field using checkboxes.
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
 *     attributes: ['class' => 'row g-3', 'data-type' => 'hobby']
 * );
 * echo $multiSelect->render();
 *
 * // Output:
 * // <div class="row g-3" data-type="hobby">
 * //     <div class="form-check">
 * //         <input type="checkbox" name="hobbies[]" value="reading" class="form-check-input">
 * //         <label class="form-check-label">Reading</label>
 * //     </div>
 * //     <div class="form-check">
 * //         <input type="checkbox" name="hobbies[]" value="traveling" class="form-check-input">
 * //         <label class="form-check-label">Traveling</label>
 * //     </div>
 * //     <div class="form-check">
 * //         <input type="checkbox" name="hobbies[]" value="gaming" class="form-check-input" checked>
 * //         <label class="form-check-label">Gaming</label>
 * //     </div>
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
                '<div class="form-check">
                    <input type="checkbox" name="%s[]" value="%s" class="form-check-input" %s>
                    <label class="form-check-label">%s</label>
                </div>',
                htmlspecialchars($this->name),
                htmlspecialchars((string)$value),
                $checked,
                htmlspecialchars($label)
            );
        }

        return sprintf(
            '<div class="row g-3" %s>%s</div>',
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
