<?php

namespace MiniCore\Form;

abstract class Field
{
    /**
     * Field name.
     *
     * @var string
     */
    protected string $name;

    /**
     * Field value.
     *
     * @var mixed
     */
    protected mixed $value;

    /**
     * Additional field attributes.
     *
     * @var array
     */
    protected array $attributes;

    /**
     * Field constructor.
     *
     * @param string $name The field name.
     * @param mixed $value The field value.
     * @param array $attributes Additional field attributes.
     */
    public function __construct(string $name, mixed $value = null, array $attributes = [])
    {
        $this->name = $name;
        $this->value = $value;
        $this->attributes = $attributes;
    }

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
     * Render the field as an HTML string.
     *
     * @return string The rendered HTML of the field.
     */
    abstract public function render(): string;
}
