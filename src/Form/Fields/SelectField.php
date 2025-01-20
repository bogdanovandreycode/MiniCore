<?php

namespace MiniCore\Form\Fields;

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
class SelectField implements FieldInterface
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
        public string $name = '',
        public mixed $value = '',
        public array $options = [],
        public array $attributes = [],
    ) {}

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
            '<select name="%s" class="form-select" %s>%s</select>',
            htmlspecialchars($this->name),
            $attributes,
            $optionsHtml
        );
    }

    /**
     * Get the name of the select field.
     *
     * @return string The name attribute.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the select field.
     *
     * @return mixed The selected option value.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the select field.
     *
     * @return array The HTML attributes as key-value pairs.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Build the additional attributes as an HTML string.
     *
     * @return string The compiled HTML attributes.
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
