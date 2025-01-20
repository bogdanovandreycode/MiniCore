<?php

namespace MiniCore\Form;

abstract class Field
{
    /**
     * Field constructor.
     *
     * @param string $name The field name.
     * @param mixed $value The field value.
     * @param array $attributes Additional field attributes.
     */
    public function __construct(
        protected string $name,
        protected string $label,
        protected mixed $value,
        protected array $attributes,
    ) {}

    /**
     * Get the name of the field.
     *
     * @return string The field name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the label of the field.
     *
     * @return string The field label.
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Get the value of the field.
     *
     * @return mixed The field value.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the field.
     *
     * @return array The field attributes.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Convert additional attributes into an HTML string.
     *
     * @return string The formatted HTML attributes.
     *
     * @example
     * // If attributes are ['class' => 'custom-class', 'id' => 'block1']
     * $field->buildAttributes(); // Output: 'class="custom-class" id="block1"'
     */
    public function buildAttributes(): string
    {
        $result = '';

        foreach ($this->attributes as $key => $value) {
            $result .= sprintf('%s="%s" ', htmlspecialchars($key), htmlspecialchars($value));
        }

        return trim($result);
    }

    /**
     * Render the field as an HTML string.
     *
     * @return string The rendered HTML of the field.
     */
    abstract public function render(): string;
}
