<?php

namespace MiniCore\Form;

/**
 * Class FormBuilder
 *
 * A flexible and dynamic builder for creating HTML forms.
 * Allows adding fields, field groups, and custom attributes to generate a complete form.
 *
 * @package MiniCore\Form
 */
class FormBuilder
{
    /**
     * @var string The form action URL (where the form will be submitted).
     */
    private string $action;

    /**
     * @var string The form method (GET, POST, etc.).
     */
    private string $method;

    /**
     * @var array List of form fields and field groups.
     */
    private array $fields = [];

    /**
     * @var array Custom attributes for the form tag.
     */
    private array $attributes = [];

    /**
     * FormBuilder constructor.
     *
     * @param string $action The form action URL.
     * @param string $method The form method (e.g., POST, GET).
     *
     * @example
     * $form = new FormBuilder('/submit', 'POST');
     */
    public function __construct(string $action = '', string $method = 'POST')
    {
        $this->action = $action;
        $this->method = strtoupper($method);
    }

    /**
     * Add a field to the form.
     *
     * @param FieldInterface|string $field The field to add (can also be raw HTML).
     * @return $this
     *
     * @example
     * $form->addField(new TextField('username'));
     */
    public function addField(FieldInterface|string $field): self
    {
        $this->fields[] = $field;
        return $this;
    }

    /**
     * Add a group of fields inside a fieldset with a legend.
     *
     * @param string $legend The legend for the fieldset.
     * @param array $fields Array of FieldInterface objects.
     * @return $this
     *
     * @example
     * $form->addGroup('User Info', [
     *     new TextField('first_name'),
     *     new TextField('last_name')
     * ]);
     */
    public function addGroup(string $legend, array $fields): self
    {
        $groupHtml = '<fieldset><legend>' . htmlspecialchars($legend) . '</legend>';

        foreach ($fields as $field) {
            if ($field instanceof FieldInterface) {
                $groupHtml .= $field->render();
            }
        }

        $groupHtml .= '</fieldset>';
        $this->fields[] = $groupHtml;
        return $this;
    }

    /**
     * Add a submit button to the form.
     *
     * @param string $label The label for the submit button.
     * @param array $attributes Additional attributes for the button.
     * @return $this
     *
     * @example
     * $form->addSubmitButton('Register', ['class' => 'btn-primary']);
     */
    public function addSubmitButton(string $label = 'Submit', array $attributes = []): self
    {
        $attrs = '';

        foreach ($attributes as $key => $value) {
            $attrs .= sprintf('%s="%s" ', htmlspecialchars($key), htmlspecialchars($value));
        }

        $this->fields[] = sprintf(
            '<button type="submit" %s>%s</button>',
            trim($attrs),
            htmlspecialchars($label)
        );

        return $this;
    }

    /**
     * Set an attribute for the form.
     *
     * @param string $name The name of the attribute.
     * @param string $value The value of the attribute.
     * @return $this
     *
     * @example
     * $form->setAttribute('class', 'custom-form');
     */
    public function setAttribute(string $name, string $value): self
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * Render the form as an HTML string.
     *
     * @return string The rendered HTML form.
     *
     * @example
     * echo $form->render();
     */
    public function render(): string
    {
        $attrs = '';

        foreach ($this->attributes as $key => $value) {
            $attrs .= sprintf('%s="%s" ', htmlspecialchars($key), htmlspecialchars($value));
        }

        $fieldsHtml = '';

        foreach ($this->fields as $field) {
            if ($field instanceof FieldInterface) {
                $fieldsHtml .= $field->render();
                continue;
            }

            $fieldsHtml .= $field; // Support raw HTML strings
        }

        return sprintf(
            '<form action="%s" method="%s" %s>%s</form>',
            htmlspecialchars($this->action),
            htmlspecialchars($this->method),
            trim($attrs),
            $fieldsHtml
        );
    }
}
