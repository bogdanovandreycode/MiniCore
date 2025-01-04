<?php

namespace Vendor\Undermarket\Core\Form;

interface FieldInterface
{
    /**
     * Render the field as an HTML string.
     *
     * @return string The rendered HTML of the field.
     */
    public function render(): string;

    /**
     * Get the name of the field.
     *
     * @return string The name attribute of the field.
     */
    public function getName(): string;

    /**
     * Get the value of the field.
     *
     * @return mixed The value of the field.
     */
    public function getValue(): mixed;

    /**
     * Get the additional attributes of the field.
     *
     * @return array The attributes as key-value pairs.
     */
    public function getAttributes(): array;

    /**
     * Build the additional attributes of the field as an HTML string.
     *
     * @return string The attributes as an HTML string.
     */
    public function buildAttributes(): string;
}
