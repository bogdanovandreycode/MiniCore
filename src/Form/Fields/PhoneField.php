<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\FieldInterface;

/**
 * Class PhoneField
 *
 * Represents a Bootstrap-styled phone number input field in a form.
 * This field generates an HTML `<input type="tel">` element for collecting phone numbers.
 * Additional attributes can be passed for customization, such as placeholders or validation patterns.
 *
 * @package MiniCore\Form\Fields
 *
 * @example
 * // Phone field with placeholder and validation pattern
 * $phoneField = new PhoneField(
 *     name: 'contact_number',
 *     attributes: [
 *         'placeholder' => '+1 (555) 123-4567',
 *         'pattern' => '[+][0-9]{1,3} [0-9]{3}-[0-9]{3}-[0-9]{4}',
 *         'class' => 'form-control',
 *         'required' => 'required'
 *     ]
 * );
 * echo $phoneField->render();
 *
 * // Output:
 * // <input type="tel" name="contact_number" value="" placeholder="+1 (555) 123-4567" pattern="[+][0-9]{1,3} [0-9]{3}-[0-9]{3}-[0-9]{4}" class="form-control" required/>
 */
class PhoneField implements FieldInterface
{
    /**
     * PhoneField constructor.
     *
     * @param string $name       The name attribute of the phone input field.
     * @param mixed  $value      The default value of the phone field.
     * @param array  $attributes Additional HTML attributes (e.g., placeholder, pattern, class).
     */
    public function __construct(
        public string $name = '',
        public mixed $value = '',
        public array $attributes = [],
    ) {}

    /**
     * Render the phone field as an HTML string.
     *
     * @return string The rendered HTML of the phone field.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        return sprintf(
            '<input type="tel" name="%s" value="%s" class="form-control" %s/>',
            htmlspecialchars($this->name),
            htmlspecialchars((string)$this->value),
            $attributes
        );
    }
    /**
     * Get the name of the phone field.
     *
     * @return string The name attribute.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the phone field.
     *
     * @return mixed The value attribute.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the phone field.
     *
     * @return array The HTML attributes as key-value pairs.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Build the additional attributes into an HTML string.
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
