<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\Field;
use MiniCore\Form\FieldInterface;

/**
 * Class TimeField
 *
 * Represents a Bootstrap-styled time input field with separate inputs for hours, minutes, and optional seconds.
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
 * // <div class="d-flex gap-3">
 * //     <div class="mb-3">
 * //         <label class="form-label">Hours</label>
 * //         <div class="input-group">
 * //             <button type="button" class="btn btn-outline-secondary" data-type="hours">▲</button>
 * //             <input type="number" name="start_time[hours]" value="9" min="0" max="23" class="form-control text-center">
 * //             <button type="button" class="btn btn-outline-secondary" data-type="hours">▼</button>
 * //         </div>
 * //     </div>
 * //     <div class="mb-3">
 * //         <label class="form-label">Minutes</label>
 * //         <div class="input-group">
 * //             <button type="button" class="btn btn-outline-secondary" data-type="minutes">▲</button>
 * //             <input type="number" name="start_time[minutes]" value="30" min="0" max="59" class="form-control text-center">
 * //             <button type="button" class="btn btn-outline-secondary" data-type="minutes">▼</button>
 * //         </div>
 * //     </div>
 * // </div>
 */
class TimeField extends Field implements FieldInterface
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
        string $name = 'time',
        string $label = '',
        array $value = ['hours' => 0, 'minutes' => 0, 'seconds' => 0],
        array $attributes = [],
        public int $interval = 1,
        public bool $includeSeconds = false,
    ) {
        parent::__construct(
            $name,
            $label,
            $value,
            $attributes
        );
    }

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
            '<div class="d-flex gap-3" %s>%s %s %s</div>',
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
            '<div class="mb-3">
                <label class="form-label">%s</label>
                <div class="input-group">
                    <button type="button" class="btn btn-outline-secondary" data-type="%s">▲</button>
                    <input type="number" name="%s" value="%d" min="0" max="%d" class="form-control text-center">
                    <button type="button" class="btn btn-outline-secondary" data-type="%s">▼</button>
                </div>
            </div>',
            htmlspecialchars($label),
            $type,
            htmlspecialchars($name),
            $value,
            $maxValue,
            $type
        );
    }
}
