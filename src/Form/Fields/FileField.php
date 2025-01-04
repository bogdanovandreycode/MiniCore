<?php

namespace Vendor\Undermarket\Core\Form\Fields;

use Vendor\Undermarket\Core\Form\FieldInterface;

class FileField implements FieldInterface
{
    public function __construct(
        public string $name = '',
        public mixed $value = null,
        public array $attributes = [],
        public string $successMessage = 'Upload successful!',
        public string $errorMessage = 'Upload failed.',
    ) {}

    /**
     * Render the file input field as a custom HTML string with progress bar.
     *
     * @return string The rendered custom file input HTML.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        return sprintf(
            '<div class="file-field">
                <input type="file" name="%s" %s>
                <div class="file-info">
                    <span class="file-name">No file chosen</span>
                </div>
                <div class="progress-bar">
                    <div class="progress"></div>
                </div>
                <div class="upload-status">
                    <span class="success-message">%s</span>
                    <span class="error-message">%s</span>
                </div>
            </div>',
            htmlspecialchars($this->name),
            $attributes,
            htmlspecialchars($this->successMessage),
            htmlspecialchars($this->errorMessage)
        );
    }

    /**
     * Get the name of the file input field.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the file input field.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the file input field.
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
