<?php

namespace Vendor\Undermarket\Core\Form\Fields;

use Vendor\Undermarket\Core\Form\FieldInterface;

class DateTimeField implements FieldInterface
{
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
            '<div class="date-field">
                <input type="text" name="%s[date]" value="%s" placeholder="%s" class="date-input">
                <div class="date-icon"><i class="fa fa-calendar"></i></div>
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
            '<div class="time-field">%s %s %s</div>',
            $hourField,
            $minuteField,
            $secondField
        );

        return sprintf(
            '<div class="datetime-field" %s>%s %s</div>',
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
     * Get the name of the datetime field.
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
