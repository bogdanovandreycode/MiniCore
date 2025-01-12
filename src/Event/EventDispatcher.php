<?php

namespace MiniCore\Event;

/**
 * Class EventDispatcher
 *
 * A simple event dispatcher that allows registering listeners and dispatching events.
 * It enables decoupling components by using events for communication.
 *
 * @package MiniCore\Event
 */
class EventDispatcher
{
    /**
     * @var array List of registered listeners grouped by event name.
     */
    private static array $listeners = [];

    /**
     * Register an event listener for a specific event.
     *
     * @param string   $eventName The name of the event to listen for.
     * @param callable $listener  The listener callback that will be triggered when the event is dispatched.
     *
     * @example
     * EventDispatcher::addListener('user.registered', function ($data) {
     *     echo "New user registered: " . $data['username'];
     * });
     */
    public static function addListener(string $eventName, callable $listener): void
    {
        self::$listeners[$eventName][] = $listener;
    }

    /**
     * Dispatch an event to all registered listeners.
     *
     * @param string $eventName The name of the event to dispatch.
     * @param array  $data      Optional data to pass to the listeners.
     *
     * @example
     * EventDispatcher::dispatch('user.registered', ['username' => 'john_doe']);
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
     * Get all registered listeners.
     *
     * This method is useful for debugging or testing to see which listeners are registered.
     *
     * @return array The list of all registered listeners grouped by event name.
     *
     * @example
     * $listeners = EventDispatcher::getListeners();
     * print_r($listeners);
     */
    public static function getListeners(): array
    {
        return self::$listeners;
    }
}
