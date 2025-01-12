<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\FieldInterface;

/**
 * Class TextIconField
 *
 * Represents a text input field with an integrated icon.
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
 *         'class' => 'search-input'
 *     ]
 * );
 * echo $searchField->render();
 *
 * // Output:
 * // <div class="text-icon-field">
 * //     <i class="fa fa-search"></i>
 * //     <input type="text" name="search" value="" placeholder="Search..." class="search-input"/>
 * // </div>
 */
class TextIconField implements FieldInterface
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
        public string $name = '',
        public mixed $value = '',
        public string $iconClass = '',
        public array $attributes = [],
    ) {}

    /**
     * Render the text field with an icon as an HTML string.
     *
     * @return string The rendered HTML of the text field with an icon.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        return sprintf(
            '<div class="text-icon-field">
                <i class="%s"></i>
                <input type="text" name="%s" value="%s" %s/>
            </div>',
            htmlspecialchars($this->iconClass),
            htmlspecialchars($this->name),
            htmlspecialchars((string)$this->value),
            $attributes
        );
    }

    /**
     * Get the name of the text field.
     *
     * @return string The name attribute.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the text field.
     *
     * @return mixed The value attribute.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the text field.
     *
     * @return array The HTML attributes as key-value pairs.
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
