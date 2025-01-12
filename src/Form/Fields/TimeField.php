<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\FieldInterface;

/**
 * Class TimeField
 *
 * Represents a customizable time input field with separate inputs for hours, minutes, and optional seconds.
 * Includes increment and decrement buttons for user-friendly time selection.
 *
 * @package MiniCore\Form\Fields
 *
 * @example
 * // Basic time input without seconds
 * $timeField = new TimeField(
 *     name: 'start_time',
 *     interval: 5,
 *     includeSeconds: false,
 *     value: ['hours' => 9, 'minutes' => 30]
 * );
 * echo $timeField->render();
 *
 * // Output:
 * // <div class="time-field">
 * //     <div class="time-input">
 * //         <label>Hours</label>
 * //         <div class="time-spinner">
 * //             <button type="button" class="increment" data-type="hours">▲</button>
 * //             <input type="number" name="start_time[hours]" value="9" min="0" max="23" class="time-hours">
 * //             <button type="button" class="decrement" data-type="hours">▼</button>
 * //         </div>
 * //     </div>
 * //     <div class="time-input">
 * //         <label>Minutes</label>
 * //         <div class="time-spinner">
 * //             <button type="button" class="increment" data-type="minutes">▲</button>
 * //             <input type="number" name="start_time[minutes]" value="30" min="0" max="59" class="time-minutes">
 * //             <button type="button" class="decrement" data-type="minutes">▼</button>
 * //         </div>
 * //     </div>
 * // </div>
 */
class TimeField implements FieldInterface
{
    /**
     * TimeField constructor.
     *
     * @param string $name The name attribute of the time input.
     * @param int $interval The step interval for incrementing/decrementing time.
     * @param bool $includeSeconds Whether to include seconds in the input.
     * @param array $value Default time value in ['hours' => int, 'minutes' => int, 'seconds' => int].
     * @param array $attributes Additional HTML attributes for the field.
     */
    public function __construct(
        public string $name = 'time',
        public int $interval = 1,
        public bool $includeSeconds = false,
        public array $value = ['hours' => 0, 'minutes' => 0, 'seconds' => 0],
        public array $attributes = [],
    ) {}

    /**
     * Render the time field as a custom HTML string with hour, minute, and optional second inputs.
     *
     * @return string The rendered custom time input HTML.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();
        $hourField = $this->renderInput('hours', 'Hours', 23);
        $minuteField = $this->renderInput('minutes', 'Minutes', 59);
        $secondField = $this->includeSeconds ? $this->renderInput('seconds', 'Seconds', 59) : '';

        return sprintf(
            '<div class="time-field" %s>%s %s %s</div>',
            $attributes,
            $hourField,
            $minuteField,
            $secondField
        );
    }

    /**
     * Render a single time input (hours, minutes, or seconds).
     */
    private function renderInput(string $type, string $label, int $maxValue): string
    {
        $value = $this->value[$type];
        $name = "{$this->name}[{$type}]";

        return sprintf(
            '<div class="time-input">
                <label>%s</label>
                <div class="time-spinner">
                    <button type="button" class="increment" data-type="%s">▲</button>
                    <input type="number" name="%s" value="%d" min="0" max="%d" class="time-%s">
                    <button type="button" class="decrement" data-type="%s">▼</button>
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
     * Get the name of the time field.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the time field.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the time field.
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
