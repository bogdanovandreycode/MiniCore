<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\FieldInterface;

/**
 * Class FileField
 *
 * Represents a customizable file upload field with progress bar and status messages.
 * This field allows users to upload files and provides feedback on upload success or failure.
 *
 * @package MiniCore\Form\Fields
 *
 * @example
 * // File upload with accepted file types and custom messages
 * $fileField = new FileField(
 *     name: 'profile_picture',
 *     attributes: ['accept' => 'image/*', 'multiple' => 'multiple'],
 *     successMessage: 'File uploaded successfully!',
 *     errorMessage: 'File upload failed. Please try again.'
 * );
 * echo $fileField->render();
 *
 * // Output:
 * // <div class="file-field">
 * //     <input type="file" name="profile_picture" accept="image/*" multiple>
 * //     <div class="file-info"><span class="file-name">No file chosen</span></div>
 * //     <div class="progress-bar"><div class="progress"></div></div>
 * //     <div class="upload-status">
 * //         <span class="success-message">File uploaded successfully!</span>
 * //         <span class="error-message">File upload failed. Please try again.</span>
 * //     </div>
 * // </div>
 */
class FileField implements FieldInterface
{
    /**
     * FileField constructor.
     *
     * @param string $name           The name attribute of the file input.
     * @param mixed  $value          The current value of the file input (usually null).
     * @param array  $attributes     Additional HTML attributes for the input field.
     * @param string $successMessage Message displayed on successful upload.
     * @param string $errorMessage   Message displayed on failed upload.
     */
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
     *
     * @return string The name attribute.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the file input field.
     *
     * @return mixed The value of the input field.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the file input field.
     *
     * @return array The key-value pairs of attributes.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Build the attributes as an HTML string.
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
