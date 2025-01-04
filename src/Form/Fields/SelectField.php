<?php

namespace Vendor\Undermarket\Core\Form\Fields;

use Vendor\Undermarket\Core\Form\FieldInterface;

class SelectField implements FieldInterface
{
    public function __construct(
        public string $name = '',
        public mixed $value = '',
        public array $options = [],
        public array $attributes = [],
    ) {}

    /**
     * Render the select field as an HTML string.
     *
     * @return string The rendered HTML of the select field
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        $optionsHtml = '';
        foreach ($this->options as $key => $label) {
            $selected = ($key == $this->value) ? 'selected' : '';
            $optionsHtml .= sprintf(
                '<option value="%s" %s>%s</option>',
                htmlspecialchars($key),
                $selected,
                htmlspecialchars($label)
            );
        }

        return sprintf(
            '<select name="%s" %s>%s</select>',
            htmlspecialchars($this->name),
            $attributes,
            $optionsHtml
        );
    }

    /**
     * Get the name of the select field.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the select field.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the select field.
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
