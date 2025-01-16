<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\FieldInterface;
use MiniCore\Form\Enums\ShapeType;

/**
 * Class AvatarField
 *
 * A custom form field designed for uploading and displaying avatar images.
 * It provides a clickable preview of the avatar and allows uploading a new image.
 *
 * @package MiniCore\Fields
 */
class AvatarField implements FieldInterface
{
    /**
     * AvatarField constructor.
     *
     * @param string $name The `name` attribute for the file input field.
     * @param mixed $value The current avatar image URL or file path. If empty, a placeholder is used.
     * @param array $attributes Additional HTML attributes for the wrapper `<div>` element.
     * @param string $placeholder The default image path if no avatar is set.
     * @param ShapeType $shape Defines the shape of the avatar preview (circle or square).
     */
    public function __construct(
        public string $name = '',
        public mixed $value = null,
        public array $attributes = [],
        public string $placeholder = 'assets/default-avatar.png',
        public ShapeType $shape = ShapeType::Circle
    ) {}

    /**
     * Render the avatar upload field with a clickable preview.
     *
     * Generates the HTML structure for the avatar upload field,
     * which includes a preview image and a hidden file input.
     *
     * @return string The rendered custom avatar field HTML.
     *
     * @example
     * $avatarField = new AvatarField('profile_picture');
     * echo $avatarField->render();
     * 
     * // Output:
     * <div class="avatar-field circle">
     *     <label>
     *         <input type="file" name="profile_picture" accept="image/*" hidden>
     *         <img src="assets/default-avatar.png" alt="Avatar" class="avatar-preview">
     *     </label>
     * </div>
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();
        $avatarUrl = $this->value ?: $this->placeholder;

        return sprintf(
            '<div class="avatar-field %s" %s>
                <label>
                    <input type="file" name="%s" accept="image/*" hidden>
                    <img src="%s" alt="Avatar" class="avatar-preview">
                </label>
            </div>',
            htmlspecialchars($this->shape->value),
            $attributes,
            htmlspecialchars($this->name),
            htmlspecialchars($avatarUrl)
        );
    }

    /**
     * Get the `name` attribute of the field.
     *
     * @return string The field's name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the current value of the avatar field.
     *
     * Typically returns the URL or path of the uploaded avatar image.
     *
     * @return mixed The current avatar value.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get additional HTML attributes for the wrapper `<div>`.
     *
     * @return array The attributes as key-value pairs.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Convert additional attributes into an HTML string.
     *
     * @return string The formatted HTML attributes.
     *
     * @example
     * // If attributes are ['class' => 'custom-avatar', 'id' => 'avatar1']
     * $field->buildAttributes(); // Output: 'class="custom-avatar" id="avatar1"'
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
