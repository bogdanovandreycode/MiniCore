<?php

namespace Vendor\Undermarket\Core\Form\Fields;

use Vendor\Undermarket\Core\Form\FieldInterface;

class PasswordField implements FieldInterface
{
    public function __construct(
        public string $name = '',
        public mixed $value = '',
        public array $attributes = [],
    ) {}

    /**
     * Render the password field as an HTML string.
     *
     * @return string The rendered HTML of the password field.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        return sprintf(
            '<input type="password" name="%s" value="%s" %s/>',
            htmlspecialchars($this->name),
            htmlspecialchars((string)$this->value),
            $attributes
        );
    }

    /**
     * Get the name of the password field.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the password field.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the password field.
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
