<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\Field;
use MiniCore\Form\FieldInterface;

/**
 * Class Captcha
 *
 * Represents a simple math-based CAPTCHA field for form validation.
 * This CAPTCHA asks the user to solve a basic math question (addition or subtraction)
 * to prevent automated form submissions.
 *
 * @package MiniCore\Form\Fields
 *
 * @example
 * $captcha = new Captcha();
 * $captcha->generate();
 * echo $captcha->render();
 * 
 * // Output:
 * <div class="mb-3">
 *     <label class="form-label">14 + 6 = ?</label>
 *     <input type="text" name="captcha" value="" class="form-control">
 * </div>
 */
class Captcha extends Field implements FieldInterface
{
    /**
     * Captcha constructor.
     *
     * @param string $name         The name attribute of the CAPTCHA input field.
     * @param string $question     The generated math question.
     * @param int    $answer       The correct answer to the CAPTCHA question.
     * @param string $userInput    The user's input for the CAPTCHA.
     * @param string $errorMessage Error message displayed when validation fails.
     * @param array  $attributes   Additional HTML attributes for the input field.
     */
    public function __construct(
        string $name = 'captcha',
        string $label = '',
        array $attributes = [],
        public string $question = '',
        public int $answer = 0,
        public string $userInput = '',
        public string $errorMessage = 'Incorrect captcha answer.',
    ) {
        parent::__construct(
            $name,
            $label,
            $userInput,
            $attributes
        );
    }

    /**
     * Generate a new math-based CAPTCHA question.
     *
     * Randomly generates a simple addition or subtraction question.
     */
    public function generate(): void
    {
        $num1 = rand(1, 20);
        $num2 = rand(1, 20);
        $operator = rand(0, 1) ? '+' : '-';

        $this->question = sprintf('%d %s %d = ?', $num1, $operator, $num2);
        $this->answer = $operator === '+' ? $num1 + $num2 : $num1 - $num2;
    }

    /**
     * Render the CAPTCHA field with Bootstrap styling.
     *
     * This method generates the CAPTCHA input field using Bootstrap classes.
     * The math question is displayed as a label (`form-label`), and the input field 
     * uses the `form-control` class for consistent styling.
     *
     * @return string The rendered HTML for the CAPTCHA field.
     */
    public function render(): string
    {
        if (!isset($this->attributesattributes['class']) || !str_contains($this->attributes['class'], 'form-control')) {
            $this->attributes['class'] = trim(($this->attributes['class'] ?? '') . ' form-control');
        }

        $attributesString = $this->buildAttributes();

        return sprintf(
            '<div class="mb-3">
            <label class="form-label">%s</label>
            <input type="text" name="%s" value="%s" %s>
        </div>',
            htmlspecialchars($this->question),
            htmlspecialchars($this->name),
            htmlspecialchars($this->userInput),
            $attributesString
        );
    }

    /**
     * Validate the user's input against the correct CAPTCHA answer.
     *
     * @param mixed $input The user's input.
     * @return bool True if the input is correct, false otherwise.
     */
    public function validate(mixed $input): bool
    {
        return (int)$input === $this->answer;
    }

    /**
     * Get the user's input for the CAPTCHA field.
     *
     * @return mixed The user's input value.
     */
    public function getValue(): mixed
    {
        return $this->userInput;
    }
}
