<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\Field;
use MiniCore\Form\FieldInterface;

/**
 * Class TextIconField
 *
 * Represents a Bootstrap-styled text input field with an integrated icon.
 * This field allows adding icons (e.g., Font Awesome, Material Icons) inside a text input for visual enhancement.
 *
 * @package MiniCore\Form\Fields
 *
 * @example
 * // Search input with a search icon
 * $searchField = new TextIconField(
 *     name: 'search',
 *     value: '',
 *     iconClass: 'fa fa-search',
 *     attributes: [
 *         'placeholder' => 'Search...',
 *         'class' => 'form-control'
 *     ]
 * );
 * echo $searchField->render();
 *
 * // Output:
 * // <div class="input-group">
 * //     <span class="input-group-text"><i class="fa fa-search"></i></span>
 * //     <input type="text" name="search" value="" placeholder="Search..." class="form-control"/>
 * // </div>
 */
class TextIconField extends Field implements FieldInterface
{
    /**
     * TextIconField constructor.
     *
     * @param string $name       The name attribute of the text input.
     * @param mixed  $value      The default value of the text input.
     * @param string $iconClass  The CSS class for the icon (e.g., Font Awesome icon class).
     * @param array  $attributes Additional HTML attributes for customization (e.g., placeholder, class).
     */
    public function __construct(
        string $name = '',
        string $label = '',
        mixed $value = '',
        array $attributes = [],
        public string $iconClass = '',
    ) {
        parent::__construct(
            $name,
            $label,
            $value,
            $attributes
        );
    }

    /**
     * Render the text field with an icon as an HTML string.
     *
     * @return string The rendered HTML of the text field with an icon.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        return sprintf(
            '<div class="input-group">
                <span class="input-group-text"><i class="%s"></i></span>
                <input type="text" name="%s" value="%s" %s/>
            </div>',
            htmlspecialchars($this->iconClass),
            htmlspecialchars($this->name),
            htmlspecialchars((string)$this->value),
            $attributes
        );
    }
}
