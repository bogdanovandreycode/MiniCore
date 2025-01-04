<?php

namespace MiniCore\Fields;

use MiniCore\Form\FieldInterface;
use MiniCore\Form\Enums\ShapeType;

class AvatarField implements FieldInterface
{
    public function __construct(
        public string $name = '',
        public mixed $value = null, // Stores the path to the uploaded avatar image
        public array $attributes = [],
        public string $placeholder = 'assets/default-avatar.png', // path to the default avatar image
        public ShapeType $shape = ShapeType::Circle
    ) {}

    /**
     * Render the avatar field as a custom HTML string with a preview.
     *
     * @return string The rendered custom avatar field HTML.
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
     * Get the name of the avatar field.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of the avatar field.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the additional attributes of the avatar field.
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
