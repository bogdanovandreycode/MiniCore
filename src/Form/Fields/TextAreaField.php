<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\FieldInterface;

/**
 * Class TextAreaField
 *
 * Represents a customizable HTML `<textarea>` field for forms.
 * This field allows users to input multi-line text. It supports additional HTML attributes
 * for customization such as setting placeholder text, rows, columns, and CSS classes.
 *
 * @package MiniCore\Form\Fields
 *
 * @example
 * // Textarea with custom attributes
 * $textArea = new TextAreaField(
 *     name: 'bio',
 *     value: '',
 *     attributes: [
 *         'placeholder' => 'Tell us about yourself...',
 *         'rows' => '5',
 *         'cols' => '40',
 *         'class' => 'form-control'
 *     ]
 * );
 * echo $textArea->render();
 *
 * // Output:
 * // <textarea name="bio" placeholder="Tell us about yourself..." rows="5" cols="40" class="form-control"></textarea>
 */
class TextAreaField implements FieldInterface
{
    /**
     * TextAreaField constructor.
     *
     * @param string $name       The name attribute of the textarea field.
     * @param mixed  $value      The default text inside the textarea.
     * @param array  $attributes Additional HTML attributes for customization (e.g., rows, cols, class).
     */
    public function __construct(
        public string $name = '',
        public mixed $value = '',
        public array $attributes = [],
    ) {}

    /**
     * Render the textarea field as an HTML string.
     *
     * @return string The rendered HTML of the textarea field.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        return sprintf(
            '<textarea name="%s" %s>%s</textarea>',
            htmlspecialchars($this->name),
            $attributes,
            htmlspecialchars((string)$this->value)
        );
    }

    /**
     * Get the name of the textarea field.
     *
     * @return string The name attribute.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the textarea field.
     *
     * @return mixed The content inside the textarea.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the textarea field.
     *
     * @return array The key-value pairs of HTML attributes.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Build the attributes as an HTML string.
     *
     * @return string The formatted HTML attributes for rendering.
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
