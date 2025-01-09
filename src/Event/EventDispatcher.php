<?php

namespace MiniCore\Event;

class EventDispatcher
{
    private static array $listeners = [];

    /**
     * Register an event listener for a specific event.
     *
     * @param string $eventName The name of the event to listen for.
     * @param callable $listener The listener callback.
     */
    public static function addListener(string $eventName, callable $listener): void
    {
        self::$listeners[$eventName][] = $listener;
    }

    /**
     * Dispatch an event to all registered listeners.
     *
     * @param string $eventName The name of the event to dispatch.
     * @param array $data Optional data to pass to the listeners.
     */
    public static function dispatch(string $eventName, array $data = []): void
    {
        if (!isset(self::$listeners[$eventName])) {
            return; // No listeners registered for this event
        }

        foreach (self::$listeners[$eventName] as $listener) {
            $listener($data);
        }
    }

    /**
     * Get all registered listeners for debugging or testing.
     *
     * @return array
     */
    public static function getListeners(): array
    {
        return self::$listeners;
    }
}
