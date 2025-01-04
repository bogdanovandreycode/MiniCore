<?php

namespace Vendor\Undermarket\Core\Form\Fields;

use Vendor\Undermarket\Core\Form\FieldInterface;

class MultiSelectField implements FieldInterface
{

    public function __construct(
        public string $name = '',
        public array $options = [],
        public array $selected = [],
        public array $attributes = [],
    ) {}

    /**
     * Render the multi-select field as a custom HTML string with checkboxes.
     *
     * @return string The rendered custom multi-select HTML.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        $optionsHtml = '';
        foreach ($this->options as $value => $label) {
            $checked = in_array($value, $this->selected, true) ? 'checked' : '';
            $optionsHtml .= sprintf(
                '<label class="multi-select-option">
                    <input type="checkbox" name="%s[]" value="%s" %s>
                    <span class="option-label">%s</span>
                </label>',
                htmlspecialchars($this->name),
                htmlspecialchars((string)$value),
                $checked,
                htmlspecialchars($label)
            );
        }

        return sprintf(
            '<div class="multi-select-field" %s>%s</div>',
            $attributes,
            $optionsHtml
        );
    }

    /**
     * Get the name of the multi-select field.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the multi-select field.
     */
    public function getValue(): mixed
    {
        return $this->selected;
    }

    /**
     * Get the additional attributes of the multi-select field.
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
