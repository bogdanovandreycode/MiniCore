<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\FieldInterface;

class TextIconField implements FieldInterface
{
    public function __construct(
        public string $name = '',
        public mixed $value = '',
        public string $iconClass = '',
        public array $attributes = [],
    ) {}

    /**
     * Render the text field with an icon as an HTML string.
     *
     * @return string The rendered HTML of the text field with an icon.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        return sprintf(
            '<div class="text-icon-field">
                <i class="%s"></i>
                <input type="text" name="%s" value="%s" %s/>
            </div>',
            htmlspecialchars($this->iconClass),
            htmlspecialchars($this->name),
            htmlspecialchars((string)$this->value),
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
