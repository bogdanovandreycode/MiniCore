<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\Field;
use MiniCore\Form\FieldInterface;

/**
 * Class TextAreaField
 *
 * Represents a Bootstrap-styled HTML `<textarea>` field for forms.
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
class TextAreaField extends Field implements FieldInterface
{
    /**
     * TextAreaField constructor.
     *
     * @param string $name       The name attribute of the textarea field.
     * @param mixed  $value      The default text inside the textarea.
     * @param array  $attributes Additional HTML attributes for customization (e.g., rows, cols, class).
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
     * Render the textarea field as an HTML string.
     *
     * @return string The rendered HTML of the textarea field.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        return sprintf(
            '<textarea name="%s" class="form-control" %s>%s</textarea>',
            htmlspecialchars($this->name),
            $attributes,
            htmlspecialchars((string)$this->value)
        );
    }
}
