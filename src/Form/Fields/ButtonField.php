<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\FieldInterface;

class ButtonField implements FieldInterface
{
    public function __construct(
        public string $name = '',
        public string $label = 'Submit',
        public mixed $value = null,
        public array $attributes = []
    ) {}

    /**
     * Render the button field as an HTML string
     *
     * @return string The rendered HTML of the button.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        return sprintf(
            '<button name="%s" value="%s" %s>%s</button>',
            htmlspecialchars($this->name),
            htmlspecialchars((string) $this->value),
            $attributes,
            htmlspecialchars($this->label)
        );
    }

    /**
     * Get the name attribute of the button.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the label (text) of the button.
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Get the value of the button.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the button.
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
