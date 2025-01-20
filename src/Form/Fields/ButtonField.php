<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\Field;
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
 * $button = new ButtonField('save', 'Save', 'save_action', ['class' => 'btn btn-success']);
 * echo $button->render();
 * 
 * // Output:
 * <button name="save" value="save_action" class="btn btn-success">Save</button>
 */
class ButtonField extends Field implements FieldInterface
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
        string $name = '',
        string $label = 'Submit',
        mixed $value = null,
        array $attributes = []
    ) {
        parent::__construct(
            $name,
            $label,
            $value,
            $attributes
        );
    }

    /**
     * Render the button field as a Bootstrap-styled HTML button.
     *
     * This method generates an HTML `<button>` element with Bootstrap classes. 
     * If no custom class is provided, it defaults to `btn btn-primary`. 
     * This ensures consistent styling across forms.
     *
     * @return string The rendered HTML of the button.
     *
     */
    public function render(): string
    {
        if (!isset($this->attributes['class']) || !str_contains($this->attributes['class'], 'btn')) {
            $this->attributes['class'] = trim(($this->attributes['class'] ?? '') . ' btn btn-primary');
        }

        $attributesString = $this->buildAttributes();

        return sprintf(
            '<button name="%s" value="%s" %s>%s</button>',
            htmlspecialchars($this->name),
            htmlspecialchars((string) $this->value),
            $attributesString,
            htmlspecialchars($this->label)
        );
    }
}
