<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\FieldInterface;

/**
 * Class NonceField
 *
 * Provides a secure hidden input field for form submissions to prevent CSRF attacks.
 * Generates and validates a unique token (nonce) for each form action to ensure data integrity.
 *
 * @package MiniCore\Form\Fields
 *
 * @example
 * // Generating a Nonce Field for a Form Submission
 * $nonceField = new NonceField(action: 'submit_form');
 * echo $nonceField->render();
 *
 * // Output:
 * // <input type="hidden" name="_nonce" value="generated_nonce_value">
 *
 * @example
 * // Validating a Nonce on Form Submission
 * $nonceField = new NonceField(action: 'submit_form');
 * $isValid = $nonceField->validate($_POST['_nonce']);
 *
 * if ($isValid) {
 *     echo "Nonce is valid. Processing form data...";
 * } else {
 *     echo "Invalid or expired nonce.";
 * }
 */
class NonceField implements FieldInterface
{
    /**
     * NonceField constructor.
     *
     * @param string $name         The name attribute of the hidden nonce input.
     * @param string $action       The action identifier for the nonce.
     * @param string $nonce        The generated nonce value.
     * @param int    $expiration   The nonce expiration time in seconds.
     * @param string $userInput    The nonce value received from the form submission.
     * @param string $errorMessage Error message when the nonce is invalid or expired.
     * @param array  $attributes   Additional HTML attributes for the nonce input.
     */
    public function __construct(
        public string $name = '_nonce',
        public string $action = 'default_action',
        public string $nonce = '',
        public int $expiration = 3600,
        public string $userInput = '',
        public string $errorMessage = 'Invalid or expired nonce.',
        public array $attributes = [],
    ) {}

    /**
     * Generate a nonce value based on the action and current time.
     */
    public function generate(): void
    {
        $this->nonce = base64_encode(hash_hmac(
            'sha256',
            $this->action . '|' . time(),
            $this->getSecretKey(),
            true
        ));
    }

    /**
     * Render the nonce field as a hidden input.
     *
     * @return string The rendered nonce field HTML.
     */
    public function render(): string
    {
        if (empty($this->nonce)) {
            $this->generate();
        }

        $attributes = $this->buildAttributes();

        return sprintf(
            '<input type="hidden" name="%s" value="%s" %s>',
            htmlspecialchars($this->name),
            htmlspecialchars($this->nonce),
            $attributes
        );
    }

    /**
     * Validate the nonce value against the generated one.
     *
     * @param mixed $input The nonce value from the request.
     * @return bool True if the nonce is valid, false otherwise.
     */
    public function validate(mixed $input): bool
    {
        if (empty($input)) {
            return false;
        }

        $decodedNonce = base64_decode($input, true);

        if (!$decodedNonce) {
            return false;
        }

        $parts = explode('|', hash_hmac('sha256', $this->action, $this->getSecretKey(), true));
        $timestamp = (int) end($parts);

        if (time() > $timestamp + $this->expiration) {
            return false; // Nonce expired
        }

        $expectedNonce = base64_encode(hash_hmac(
            'sha256',
            $this->action . '|' . $timestamp,
            $this->getSecretKey(),
            true
        ));

        return hash_equals($expectedNonce, $input);
    }

    /**
     * Get the name of the nonce field.
     *
     * @return string The name attribute of the field.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the user-provided nonce value.
     *
     * @return mixed The user's input value.
     */
    public function getValue(): mixed
    {
        return $this->userInput;
    }

    /**
     * Get additional HTML attributes for the nonce field.
     *
     * @return array The attributes as key-value pairs.
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

    /**
     * Retrieve the secret key used for nonce generation and validation.
     *
     * @return string|null The secret key.
     */
    private function getSecretKey(): ?string
    {
        return $_ENV['SECRET_KEY'] ?? null;
    }
}
