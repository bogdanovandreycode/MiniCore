<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\FieldInterface;

/**
 * Class DateTimeField
 *
 * Represents a Bootstrap-styled date and time input field for forms.
 * This field includes a date picker and separate inputs for hours, minutes, and optionally seconds.
 *
 * @package MiniCore\Form\Fields
 *
 * @example
 * // DateTime field without seconds
 * $dateTimeField = new DateTimeField(name: 'appointment_datetime');
 * echo $dateTimeField->render();
 *
 * // Output:
 * // <div class="mb-3">
 * //   <div class="input-group">
 * //       <input type="text" name="appointment_datetime[date]" value="" placeholder="Select a date" class="form-control">
 * //       <span class="input-group-text"><i class="fa fa-calendar"></i></span>
 * //   </div>
 * //   <div class="d-flex gap-2">
 * //       [Hour input] [Minute input]
 * //   </div>
 * // </div>
 */
class DateTimeField implements FieldInterface
{
    /**
     * DateTimeField constructor.
     *
     * @param string $name             The name attribute of the field.
     * @param mixed  $dateValue        The pre-filled value for the date input.
     * @param mixed  $timeValue        Array containing 'hours', 'minutes', and 'seconds' values.
     * @param bool   $includeSeconds   Whether to include the seconds input.
     * @param int    $interval         Interval for time selection (e.g., 1, 5, 15 minutes).
     * @param array  $attributes       Additional HTML attributes for the field.
     * @param string $placeholderDate  Placeholder text for the date input.
     */
    public function __construct(
        private string $name = 'datetime',
        public mixed $dateValue = null,
        public mixed $timeValue = ['hours' => 0, 'minutes' => 0, 'seconds' => 0],
        public bool $includeSeconds = false,
        public int $interval = 1,
        public array $attributes = [],
        public string $placeholderDate = 'Select a date',
    ) {}

    /**
     * Render the DateTimeField as a custom HTML string.
     *
     * @return string The rendered custom datetime field HTML.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        // Rendering date field
        $dateField = sprintf(
            '<div class="input-group mb-2">
                <input type="text" name="%s[date]" value="%s" placeholder="%s" class="form-control">
                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
            </div>',
            htmlspecialchars($this->name),
            htmlspecialchars((string)$this->dateValue),
            htmlspecialchars($this->placeholderDate)
        );

        // Rendering time fields
        $hourField = $this->renderTimeInput('hours', 'Hours', 23);
        $minuteField = $this->renderTimeInput('minutes', 'Minutes', 59);
        $secondField = $this->includeSeconds ? $this->renderTimeInput('seconds', 'Seconds', 59) : '';

        $timeField = sprintf(
            '<div class="d-flex gap-2">%s %s %s</div>',
            $hourField,
            $minuteField,
            $secondField
        );

        return sprintf(
            '<div class="mb-3" %s>%s %s</div>',
            $attributes,
            $dateField,
            $timeField
        );
    }

    /**
     * Render a single time input (hours, minutes, or seconds).
     */
    private function renderTimeInput(string $type, string $label, int $maxValue): string
    {
        $value = $this->timeValue[$type];
        $name = "{$this->name}[time][{$type}]";

        return sprintf(
            '<div class="time-input">
                <label class="form-label">%s</label>
                <div class="input-group">
                    <button type="button" class="btn btn-outline-secondary increment" data-type="%s">▲</button>
                    <input type="number" name="%s" value="%d" min="0" max="%d" class="form-control text-center time-%s">
                    <button type="button" class="btn btn-outline-secondary decrement" data-type="%s">▼</button>
                </div>
            </div>',
            htmlspecialchars($label),
            $type,
            htmlspecialchars($name),
            $value,
            $maxValue,
            $type,
            $type
        );
    }

    /**
     * Get the name of the datetime field.
     *
     * @return string The name attribute.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the datetime field.
     *
     * @return array The combined date and time value.
     */
    public function getValue(): mixed
    {
        return [
            'date' => $this->dateValue,
            'time' => $this->timeValue,
        ];
    }

    /**
     * Get additional attributes of the datetime field.
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
