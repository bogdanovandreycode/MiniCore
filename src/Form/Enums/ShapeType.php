<?php

namespace MiniCore\Form\Enums;

/**
 * Enum ShapeType
 *
 * Defines the shape options for UI components such as avatar previews or buttons.
 * This enumeration allows consistent shape selection across the project.
 *
 * @package MiniCore\Form\Enums
 */
enum ShapeType: string
{
    /**
     * Represents a circular shape.
     */
    case Circle = 'circle';

    /**
     * Represents a square shape.
     */
    case Square = 'square';
}
