<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\Field;
use MiniCore\Form\FieldInterface;

/**
 * Class CodeField
 *
 * Represents a Bootstrap-styled multi-input field used for entering verification codes, 
 * such as SMS confirmation codes or security tokens.
 * 
 * This field renders a series of single-character input fields, styled with Bootstrap, 
 * where each field accepts one character of the entire code.
 *
 * @package MiniCore\Form\Fields
 *
 * @example
 * // 6-digit numeric code input for SMS verification
 * $codeField = new CodeField(
 *     name: 'sms_code',
 *     length: 6,
 *     allowOnlyDigits: true,
 *     attributes: ['placeholder' => '0']
 * );
 * echo $codeField->render();
 *
 * // Output:
 * // <div class="d-flex gap-2">
 * //   <input type="text" name="sms_code[0]" value="" maxlength="1" class="form-control text-center" placeholder="0">
 * //   <input type="text" name="sms_code[1]" value="" maxlength="1" class="form-control text-center" placeholder="0">
 * //   <input type="text" name="sms_code[2]" value="" maxlength="1" class="form-control text-center" placeholder="0">
 * //   <input type="text" name="sms_code[3]" value="" maxlength="1" class="form-control text-center" placeholder="0">
 * //   <input type="text" name="sms_code[4]" value="" maxlength="1" class="form-control text-center" placeholder="0">
 * //   <input type="text" name="sms_code[5]" value="" maxlength="1" class="form-control text-center" placeholder="0">
 * // </div>
 */

class CodeField extends Field implements FieldInterface
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
        string $name = 'code',
        string $label = 'Code',
        array $value = [],
        array $attributes = [],
        public int $length = 6,
        public bool $allowOnlyDigits = true,
    ) {
        parent::__construct(
            $name,
            $label,
            $value,
            $attributes
        );
    }

    /**
     * Render the code field as a set of Bootstrap-styled input fields.
     *
     * This method generates a group of single-character input fields using Bootstrap classes. 
     * The inputs are displayed inline with consistent spacing and centered text for easy code entry.
     *
     * @return string The rendered code field HTML.
     */
    public function render(): string
    {
        if (!isset($this->attributes['class']) || !str_contains($this->attributes['class'], 'form-control')) {
            $this->attributes['class'] = trim(($this->attributes['class'] ?? '') . ' form-control text-center');
        }

        $attributesString = $this->buildAttributes();
        $inputsHtml = '';

        for ($i = 0; $i < $this->length; $i++) {
            $inputValue = $this->value[$i] ?? '';
            $inputsHtml .= sprintf(
                '<input type="text" name="%s[%d]" value="%s" maxlength="1" %s>',
                htmlspecialchars($this->name),
                $i,
                htmlspecialchars($inputValue),
                $attributesString
            );
        }

        return sprintf('<div class="d-flex gap-2">%s</div>', $inputsHtml);
    }
}
