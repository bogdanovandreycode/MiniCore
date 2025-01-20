<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\Field;
use MiniCore\Form\FieldInterface;

/**
 * Class SelectField
 *
 * Represents a Bootstrap-styled dropdown select field in a form.
 * Allows the user to choose one option from a list of predefined options.
 * Additional HTML attributes can be applied for customization (e.g., CSS classes, styles).
 *
 * @package MiniCore\Form\Fields
 *
 * @example
 * // Select field with custom attributes and no default value
 * $selectField = new SelectField(
 *     name: 'gender',
 *     options: [
 *         'male' => 'Male',
 *         'female' => 'Female',
 *         'other' => 'Other'
 *     ],
 *     attributes: [
 *         'class' => 'form-select',
 *         'required' => 'required'
 *     ]
 * );
 * echo $selectField->render();
 *
 * // Output:
 * // <select name="gender" class="form-select" required>
 * //     <option value="male">Male</option>
 * //     <option value="female">Female</option>
 * //     <option value="other">Other</option>
 * // </select>
 */
class SelectField extends Field implements FieldInterface
{
    /**
     * SelectField constructor.
     *
     * @param string $name       The name attribute of the select field.
     * @param mixed  $value      The selected option value.
     * @param array  $options    The list of options in the format ['value' => 'Label'].
     * @param array  $attributes Additional HTML attributes (e.g., class, style).
     */
    public function __construct(
        string $name = '',
        string $label = '',
        mixed $value = '',
        array $attributes = [],
        public array $options = [],
    ) {
        parent::__construct(
            $name,
            $label,
            $value,
            $attributes
        );
    }

    /**
     * Render the select field as an HTML string.
     *
     * @return string The rendered HTML of the select field.
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
}
