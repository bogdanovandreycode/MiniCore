<?php

namespace Vendor\Undermarket\Core\Form\Fields;

use Vendor\Undermarket\Core\Form\FieldInterface;

class Captcha implements FieldInterface
{
    public function __construct(
        public string $name = 'captcha',
        public string $question = '',
        public int $answer = 0,
        public string $userInput = '', // The user's input
        public string $errorMessage = 'Incorrect captcha answer.',
        public array $attributes = [],
    ) {}

    /**
     * Generate a new captcha question and calculate the correct answer.
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
     * Render the captcha field as a custom HTML string.
     *
     * @return string The rendered captcha field HTML.
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
     * Validate the user's input against the captcha answer.
     *
     * @param mixed $input The user's input.
     * @return bool True if the input is correct, false otherwise.
     */
    public function validate(mixed $input): bool
    {
        return (int)$input === $this->answer;
    }

    /**
     * Get the name of the captcha field.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the captcha field (user's input).
     */
    public function getValue(): mixed
    {
        return $this->userInput;
    }

    /**
     * Get the additional attributes of the captcha field.
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
