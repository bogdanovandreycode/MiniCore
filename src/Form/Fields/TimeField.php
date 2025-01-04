<?php

namespace Vendor\Undermarket\Core\Form\Fields;

use Vendor\Undermarket\Core\Form\FieldInterface;

class TimeField implements FieldInterface
{
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
