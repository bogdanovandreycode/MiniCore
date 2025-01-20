<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\Field;
use MiniCore\Form\FieldInterface;

/**
 * Class TextField
 *
 * Represents a Bootstrap-styled HTML `<input type="text">` field.
 * This class allows the creation of customizable text input fields for forms with
 * support for additional attributes like placeholder, maxlength, CSS classes, etc.
 *
 * @package MiniCore\Form\Fields
 *
 * @example
 * // Text input with additional attributes
 * $textField = new TextField(
 *     name: 'search',
 *     value: '',
 *     attributes: [
 *         'placeholder' => 'Search...',
 *         'class' => 'form-control',
 *         'maxlength' => '50'
 *     ]
 * );
 * echo $textField->render();
 *
 * // Output:
 * // <input type="text" name="search" value="" placeholder="Search..." class="form-control" maxlength="50"/>
 */
class TextField extends Field implements FieldInterface
{
    /**
     * TextField constructor.
     *
     * @param string $name       The name attribute of the text input.
     * @param mixed  $value      The default value of the text input.
     * @param array  $attributes Additional HTML attributes for customization (e.g., placeholder, maxlength, class).
     */
    public function __construct(
        string $name = '',
        string $label = '',
        mixed $value = '',
        array $attributes = [],
    ) {
        parent::__construct(
            $name,
            $label,
            $value,
            $attributes
        );
    }

    /**
     * Render the text field as an HTML string.
     *
     * @return string The rendered HTML of the text field.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        return sprintf(
            '<input type="text" name="%s" value="%s" %s/>',
            htmlspecialchars($this->name),
            htmlspecialchars((string)$this->value),
            $attributes
        );
    }
}
