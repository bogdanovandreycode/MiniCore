<?php

namespace Vendor\Undermarket\Core\Form\Fields;

use Vendor\Undermarket\Core\Form\FieldInterface;

class DateField implements FieldInterface
{
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
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the date field.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the date field.
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
