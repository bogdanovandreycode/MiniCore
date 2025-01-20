<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\Field;
use MiniCore\Form\FieldInterface;

/**
 * Class FileField
 *
 * Represents a Bootstrap-styled file upload field with progress bar and status messages.
 * This field allows users to upload files and provides feedback on upload success or failure.
 *
 * @package MiniCore\Form\Fields
 *
 * @example
 * // File upload with accepted file types and custom messages
 * $fileField = new FileField(
 *     name: 'profile_picture',
 *     attributes: ['accept' => 'image/*', 'multiple' => 'multiple', 'class' => 'form-control'],
 *     successMessage: 'File uploaded successfully!',
 *     errorMessage: 'File upload failed. Please try again.'
 * );
 * echo $fileField->render();
 *
 * // Output:
 * // <div class="mb-3">
 * //     <input type="file" name="profile_picture" class="form-control" accept="image/*" multiple>
 * //     <div class="form-text">
 * //         <span class="file-name">No file chosen</span>
 * //     </div>
 * //     <div class="progress">
 * //         <div class="progress-bar" role="progressbar" style="width: 0;"></div>
 * //     </div>
 * //     <div class="mt-2">
 * //         <span class="text-success">File uploaded successfully!</span>
 * //         <span class="text-danger">File upload failed. Please try again.</span>
 * //     </div>
 * // </div>
 */
class FileField extends Field implements FieldInterface
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
        string $label = '',
        mixed $value = null,
        array $attributes = [],
        public string $successMessage = 'Upload successful!',
        public string $errorMessage = 'Upload failed.',
    ) {
        parent::__construct(
            $name,
            $label,
            $value,
            $attributes
        );
    }

    /**
     * Render the file input field as a custom HTML string with progress bar.
     *
     * @return string The rendered custom file input HTML.
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();

        return sprintf(
            '<div class="mb-3">
                <input type="file" name="%s" %s>
                <div class="form-text">
                    <span class="file-name">No file chosen</span>
                </div>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 0;"></div>
                </div>
                <div class="mt-2">
                    <span class="text-success">%s</span>
                    <span class="text-danger">%s</span>
                </div>
            </div>',
            htmlspecialchars($this->name),
            $attributes,
            htmlspecialchars($this->successMessage),
            htmlspecialchars($this->errorMessage)
        );
    }
}
