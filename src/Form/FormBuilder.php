<?php

namespace MiniCore\Form;

class FormBuilder
{
    private string $action;
    private string $method;
    private array $fields = [];
    private array $attributes = [];

    /**
     * FormBuilder constructor.
     *
     * @param string $action The form action URL.
     * @param string $method The form method (e.g., POST, GET).
     */
    public function __construct(string $action = '', string $method = 'POST')
    {
        $this->action = $action;
        $this->method = strtoupper($method);
    }

    /**
     * Add a field to the form.
     *
     * @param FieldInterface|string $field The field to add (can also be HTML string).
     * @return $this
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

            $fieldsHtml .= $field; // Direct HTML strings for flexibility
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
