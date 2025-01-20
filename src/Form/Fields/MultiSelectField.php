<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\Field;
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
class MultiSelectField extends Field implements FieldInterface
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
        string $name = '',
        string $label = '',
        array $attributes = [],
        public array $options = [],
        public array $selected = [],
    ) {
        parent::__construct(
            $name,
            $label,
            $selected,
            $attributes
        );
    }

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
                    <input type="checkbox" name="%s[]" value="%s" %s>
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
     * Get the selected values of the multi-select field.
     *
     * @return array The selected values.
     */
    public function getValue(): mixed
    {
        return $this->selected;
    }
}
