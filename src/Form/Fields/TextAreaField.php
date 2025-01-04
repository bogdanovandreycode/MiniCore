<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\FieldInterface;

class TextAreaField implements FieldInterface
{
    public function __construct(
        public string $name = '',
        public mixed $value = '',
        public array $attributes = [],
    ) {}

    /**
     * Render the textarea field as an HTML string.
     *
     * @return string The rendered HTML of the textarea field.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        return sprintf(
            '<textarea name="%s" %s>%s</textarea>',
            htmlspecialchars($this->name),
            $attributes,
            htmlspecialchars((string)$this->value)
        );
    }

    /**
     * Get the name of the textarea field.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the textarea field.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the textarea field.
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
