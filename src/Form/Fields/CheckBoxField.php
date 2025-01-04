<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\FieldInterface;

class CheckBoxField implements FieldInterface
{
    public function __construct(
        public string $name = '',
        public mixed $value = '',
        public bool $checked = false,
        public array $attributes = [],
    ) {}

    /**
     * Render the checkbox field as a custom HTML toggle switch.
     *
     * @return string The rendered custom checkbox HTML.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();
        $checked = $this->checked ? 'checked' : '';

        return sprintf(
            '<label class="switch">
                <input type="checkbox" name="%s" value="%s" %s %s>
                <span class="slider"></span>
            </label>',
            htmlspecialchars($this->name),
            htmlspecialchars((string)$this->value),
            $attributes,
            $checked
        );
    }

    /**
     * Get the name of the checkbox field.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the checkbox field.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the checkbox field.
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
