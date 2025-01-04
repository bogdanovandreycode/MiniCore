<?php

namespace Vendor\Undermarket\Core\Form\Fields;

use Vendor\Undermarket\Core\Form\FieldInterface;

class NonceField implements FieldInterface
{
    public function __construct(
        public string $name = '_nonce',
        public string $action = 'default_action',
        public string $nonce = '',
        public int $expiration = 3600, // The nonce expiration time in seconds
        public string $userInput = '', // Input value from the request
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
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the nonce field (user's input).
     */
    public function getValue(): mixed
    {
        return $this->userInput;
    }

    /**
     * Get the additional attributes of the nonce field.
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
     * Get the secret key used to generate and validate nonces.
     */
    private function getSecretKey(): ?string
    {
        return $_ENV['SECRET_KEY'] ?? null;
    }
}
