<?php

namespace MiniCore\Form\Fields;

use MiniCore\Form\Field;
use MiniCore\Form\FieldInterface;
use MiniCore\Form\Enums\ShapeType;

/**
 * Class AvatarField
 *
 * A custom form field designed for uploading and displaying avatar images.
 * It provides a clickable preview of the avatar and allows uploading a new image.
 *
 * @example
 * $avatarField = new AvatarField('profile_picture');
 * echo $avatarField->render();
 * 
 * // Output:
 * <div class="mb-3">
 *     <label class="form-label">
 *         <input type="file" name="profile_picture" accept="image/*" hidden>
 *         <img src="assets/default-avatar.png" alt="Avatar" class="img-fluid rounded-circle">
 *     </label>
 * </div>
 * 
 * @package MiniCore\Fields
 */
class AvatarField extends Field implements FieldInterface
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
        string $name = '',
        string $label = '',
        mixed $value = null,
        array $attributes = [],
        public string $placeholder = 'assets/default-avatar.png',
        public ShapeType $shape = ShapeType::Circle
    ) {
        parent::__construct(
            $name,
            $label,
            $value,
            $attributes
        );
    }

    /**
     * Render the avatar upload field with Bootstrap styling.
     *
     * This method generates the HTML structure for an avatar upload field 
     * using Bootstrap classes. It includes a preview image styled as either 
     * a circle (`rounded-circle`) or a square (`img-thumbnail`) and a hidden 
     * file input for uploading a new avatar image. The preview is clickable 
     * and triggers the file upload dialog.
     *
     * @return string The rendered HTML for the avatar upload field.
     *
     */
    public function render(): string
    {
        $attributes = $this->buildAttributes();
        $avatarUrl = $this->value ?: $this->placeholder;
        $shapeClass = $this->shape === ShapeType::Circle ? 'rounded-circle' : 'img-thumbnail';

        return sprintf(
            '<div class="mb-3" %s>
            <label class="form-label">
                <input type="file" name="%s" accept="image/*" hidden>
                <img src="%s" alt="Avatar" class="img-fluid %s">
            </label>
        </div>',
            $attributes,
            htmlspecialchars($this->name),
            htmlspecialchars($avatarUrl),
            $shapeClass
        );
    }
}
