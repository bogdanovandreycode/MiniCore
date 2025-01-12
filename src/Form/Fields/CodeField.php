<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\FieldInterface;

/**
 * Class CodeField
 *
 * Represents a multi-input field typically used for entering verification codes, 
 * such as OTPs (One-Time Passwords) or security codes.
 * 
 * This field renders several single-character input fields, where each field 
 * accepts one character of the entire code.
 *
 * @package MiniCore\Form\Fields
 *
 * @example
 * // 4-character alphanumeric code field with custom styling
 * $codeField = new CodeField(
 *     name: 'auth_code',
 *     length: 4,
 *     allowOnlyDigits: false,
 *     attributes: ['class' => 'auth-code-input', 'placeholder' => '*']
 * );
 * echo $codeField->render();
 *
 * // Output:
 * // <div class="code-field">
 * //   <input type="text" name="auth_code[0]" value="" maxlength="1" class="auth-code-input" placeholder="*">
 * //   <input type="text" name="auth_code[1]" value="" maxlength="1" class="auth-code-input" placeholder="*">
 * //   <input type="text" name="auth_code[2]" value="" maxlength="1" class="auth-code-input" placeholder="*">
 * //   <input type="text" name="auth_code[3]" value="" maxlength="1" class="auth-code-input" placeholder="*">
 * // </div>
 */
class CodeField implements FieldInterface
{
    /**
     * CodeField constructor.
     *
     * @param string $name           The name attribute of the field.
     * @param int    $length         Number of input fields to generate (default: 6).
     * @param bool   $allowOnlyDigits Whether only digits are allowed (default: true).
     * @param array  $value          Pre-filled values for the code input fields.
     * @param array  $attributes     Additional HTML attributes for each input field.
     */
    public function __construct(
        public string $name = 'code',
        public int $length = 6,
        public bool $allowOnlyDigits = true,
        public array $value = [],
        public array $attributes = [],
    ) {}

    /**
     * Render the code field as a set of input fields.
     *
     * @return string The rendered code field HTML.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();
        $inputsHtml = '';

        for ($i = 0; $i < $this->length; $i++) {
            $inputValue = $this->value[$i] ?? '';
            $inputsHtml .= sprintf(
                '<input type="text" name="%s[%d]" value="%s" maxlength="1" class="code-input" %s>',
                htmlspecialchars($this->name),
                $i,
                htmlspecialchars($inputValue),
                $attributes
            );
        }

        return sprintf('<div class="code-field">%s</div>', $inputsHtml);
    }

    /**
     * Get the name of the code field.
     *
     * @return string The name attribute of the input field.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the code field.
     *
     * @return mixed The values of the input fields as an array.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the code field.
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
