<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\FieldInterface;

/**
 * Class DateField
 *
 * Represents a customizable date input field for forms.
 * This field generates an HTML input for date selection with a user-friendly interface.
 *
 * @package MiniCore\Form\Fields
 *
 * @example
 * // Basic date input field
 * $dateField = new DateField(name: 'birth_date');
 * echo $dateField->render();
 *
 * // Output:
 * // <div class="date-field">
 * //     <input type="text" name="birth_date" value="" placeholder="Select a date" class="date-input">
 * //     <div class="date-icon"><i class="fa fa-calendar"></i></div>
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
 * // <div class="date-field">
 * //     <input type="text" name="event_date" value="2024-12-31" placeholder="Pick a date" class="custom-date" id="event-date">
 * //     <div class="date-icon"><i class="fa fa-calendar"></i></div>
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
     * Render the date field as a custom HTML string with a user-friendly interface.
     *
     * @return string The rendered custom date input HTML.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        return sprintf(
            '<div class="date-field">
                <input type="text" name="%s" value="%s" placeholder="%s" class="date-input" %s>
                <div class="date-icon"><i class="fa fa-calendar"></i></div>
            </div>',
            htmlspecialchars($this->name),
            htmlspecialchars((string)$this->value),
            htmlspecialchars($this->placeholder),
            $attributes
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
