<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\FieldInterface;

/**
 * Class DateField
 *
 * Represents a Bootstrap-styled date input field for forms.
 * This field generates an HTML `<input type="date">` with Bootstrap styling 
 * and an optional calendar icon for a more user-friendly interface.
 *
 * @package MiniCore\Form\Fields
 *
 * @example
 * // Basic date input field
 * $dateField = new DateField(name: 'birth_date');
 * echo $dateField->render();
 *
 * // Output:
 * // <div class="input-group mb-3">
 * //     <input type="date" name="birth_date" value="" placeholder="Select a date" class="form-control">
 * //     <span class="input-group-text"><i class="fa fa-calendar"></i></span>
 * // </div>
 *
 * @example
 * // Date input field with default value and custom attributes
 * $dateField = new DateField(
 *     name: 'event_date',
 *     value: '2024-12-31',
 *     attributes: ['class' => 'custom-date', 'id' => 'event-date'],
 *     placeholder: 'Pick a date'
 * );
 * echo $dateField->render();
 *
 * // Output:
 * // <div class="input-group mb-3">
 * //     <input type="date" name="event_date" value="2024-12-31" placeholder="Pick a date" class="form-control custom-date" id="event-date">
 * //     <span class="input-group-text"><i class="fa fa-calendar"></i></span>
 * // </div>
 */
class DateField implements FieldInterface
{
    /**
     * DateField constructor.
     *
     * @param string $name        The name attribute of the date input field.
     * @param mixed  $value       The pre-filled value for the input field (e.g., '2024-01-01').
     * @param array  $attributes  Additional HTML attributes (e.g., class, id).
     * @param string $placeholder Placeholder text for the input field.
     */
    public function __construct(
        public string $name = '',
        public mixed $value = null,
        public array $attributes = [],
        public string $placeholder = 'Select a date',
    ) {}

    /**
     * Render the date field with Bootstrap styling.
     *
     * @return string The rendered HTML for the date input field.
     */
    public function render(): string
    {
        if (!isset($this->attributes['class']) || !str_contains($this->attributes['class'], 'form-control')) {
            $this->attributes['class'] = trim(($this->attributes['class'] ?? '') . ' form-control');
        }

        $attributesString = $this->buildAttributes();

        return sprintf(
            '<div class="input-group mb-3">
                <input type="date" name="%s" value="%s" placeholder="%s" %s>
                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
            </div>',
            htmlspecialchars($this->name),
            htmlspecialchars((string)$this->value),
            htmlspecialchars($this->placeholder),
            $attributesString
        );
    }

    /**
     * Get the name of the date field.
     *
     * @return string The name attribute of the field.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the date field.
     *
     * @return mixed The current value of the date input.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the date field.
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
