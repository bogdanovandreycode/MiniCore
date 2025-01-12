<?php

namespace MiniCore\Form\Fields;

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
 * // Creating and rendering a CAPTCHA field
 * $captcha = new Captcha();
 * $captcha->generate(); // Generates a random math question
 * echo $captcha->render();
 * 
 * // Output:
 * // <div class="captcha-field">
 * //     <label class="captcha-question">14 + 6 = ?</label>
 * //     <input type="text" name="captcha" value="" class="captcha-input">
 * // </div>
 *
 * @example
 * // Validating user input
 * $captcha = new Captcha();
 * $captcha->generate();
 * 
 * // Simulate user input
 * $userInput = 20;
 * 
 * if ($captcha->validate($userInput)) {
 *     echo "CAPTCHA passed!";
 * } else {
 *     echo "Incorrect answer. Please try again.";
 * }
 */
class Captcha implements FieldInterface
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
        public string $name = 'captcha',
        public string $question = '',
        public int $answer = 0,
        public string $userInput = '',
        public string $errorMessage = 'Incorrect captcha answer.',
        public array $attributes = [],
    ) {}

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
     * Render the CAPTCHA field as a custom HTML block.
     *
     * @return string The rendered HTML for the CAPTCHA field.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        return sprintf(
            '<div class="captcha-field">
                <label class="captcha-question">%s</label>
                <input type="text" name="%s" value="%s" class="captcha-input" %s>
            </div>',
            htmlspecialchars($this->question),
            htmlspecialchars($this->name),
            htmlspecialchars($this->userInput),
            $attributes
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
     * Get the name of the CAPTCHA field.
     *
     * @return string The name attribute.
     */
    public function getName(): string
    {
        return $this->name;
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

    /**
     * Get the additional HTML attributes for the CAPTCHA input field.
     *
     * @return array The attributes as key-value pairs.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Build the HTML attributes into a string for rendering.
     *
     * @return string The formatted HTML attributes.
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
