<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\FieldInterface;

class CodeField implements FieldInterface
{
    public function __construct(
        public string $name = 'code',
        public int $length = 6,
        public bool $allowOnlyDigits = true,
        public array $value = [],
        public array $attributes = [],
    ) {}

    /**
     * Render the code field as a set of input fields.
     *
     * @return string The rendered code field HTML.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();
        $inputsHtml = '';

        for ($i = 0; $i < $this->length; $i++) {
            $inputValue = $this->value[$i] ?? '';
            $inputsHtml .= sprintf(
                '<input type="text" name="%s[%d]" value="%s" maxlength="1" class="code-input" %s>',
                htmlspecialchars($this->name),
                $i,
                htmlspecialchars($inputValue),
                $attributes
            );
        }

        return sprintf('<div class="code-field">%s</div>', $inputsHtml);
    }

    /**
     * Get the name of the code field.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the code field.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the code field.
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
