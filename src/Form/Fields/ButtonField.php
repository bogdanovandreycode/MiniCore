<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\FieldInterface;

/**
 * Class ButtonField
 *
 * Represents a customizable HTML button field in a form.
 * This class allows for the creation of different types of buttons with flexible attributes.
 *
 * @package MiniCore\Form\Fields
 *
 * @example
 * // Simple submit button
 * $submitButton = new ButtonField('submit', 'Send');
 * echo $submitButton->render();
 * // Output: <button name="submit" value="">Send</button>
 *
 * @example
 * // Button with custom attributes
 * $button = new ButtonField(
 *     'save',
 *     'Save Changes',
 *     'save_action',
 *     ['class' => 'btn btn-success', 'id' => 'save-btn']
 * );
 * echo $button->render();
 * // Output: <button name="save" value="save_action" class="btn btn-success" id="save-btn">Save Changes</button>
 */
class ButtonField implements FieldInterface
{
    /**
     * ButtonField constructor.
     *
     * @param string $name       The name attribute of the button (used for form submission).
     * @param string $label      The text displayed on the button.
     * @param mixed  $value      The value sent when the button is clicked.
     * @param array  $attributes Additional HTML attributes for the button (e.g., class, id, style).
     */
    public function __construct(
        public string $name = '',
        public string $label = 'Submit',
        public mixed $value = null,
        public array $attributes = []
    ) {}

    /**
     * Render the button field as an HTML string.
     *
     * @return string The rendered HTML of the button.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        return sprintf(
            '<button name="%s" value="%s" %s>%s</button>',
            htmlspecialchars($this->name),
            htmlspecialchars((string) $this->value),
            $attributes,
            htmlspecialchars($this->label)
        );
    }

    /**
     * Get the name attribute of the button.
     *
     * @return string The name attribute.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the label (text) of the button.
     *
     * @return string The button text.
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Get the value of the button.
     *
     * @return mixed The button value.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the button.
     *
     * @return array The key-value pairs of attributes.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Build the attributes as an HTML string.
     *
     * @return string The HTML-ready string of attributes.
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
