<?php

namespace MiniCore\Event;

/**
 * Interface EventInterface
 *
 * Defines the structure for custom events within the application.
 * All event classes must implement this interface to be dispatched
 * and handled by the EventDispatcher.
 *
 * @package MiniCore\Event
 */
interface EventInterface
{
    /**
     * Get the name of the event.
     *
     * The event name is used to identify the event when dispatching
     * and when adding listeners for the event.
     *
     * @return string The unique name of the event.
     *
     * @example
     * return 'user.registered';
     */
    public function getName(): string;

    /**
     * Get the event data.
     *
     * Returns an array of data that is passed along with the event.
     * This data can contain any relevant information about the event.
     *
     * @return array The event data.
     *
     * @example
     * return ['username' => 'john_doe', 'email' => 'john@example.com'];
     */
    public function getData(): array;
}
