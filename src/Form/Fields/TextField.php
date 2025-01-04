<?php

namespace Vendor\Undermarket\Core\Form\Fields;

use Vendor\Undermarket\Core\Form\FieldInterface;

class TextField implements FieldInterface
{
    public function __construct(
        public string $name = '',
        public mixed $value = '',
        public array $attributes = [],
    ) {}

    /**
     * Render the text field as an HTML string.
     *
     * @return string The rendered HTML of the text field
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        return sprintf(
            '<input type="text" name="%s" value="%s" %s/>',
            htmlspecialchars($this->name),
            htmlspecialchars((string) $this->value),
            $attributes
        );
    }

    /**
     * Get the name of the text field.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the text field.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the text field.
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
