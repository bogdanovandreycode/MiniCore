<?php

namespace MiniCore\Tests\Event;

use PHPUnit\Framework\TestCase;
use MiniCore\Event\EventDispatcher;

/**
 * Unit tests for the EventDispatcher class.
 *
 * This test suite verifies the core event handling mechanism,
 * including registering listeners, dispatching events,
 * and retrieving registered listeners.
 *
 * Covered functionality:
 * - Adding event listeners.
 * - Dispatching events to single or multiple listeners.
 * - Handling events without registered listeners.
 * - Retrieving all registered listeners.
 */
class EventDispatcherTest extends TestCase
{
    /**
     * Clears all registered listeners before each test.
     */
    protected function setUp(): void
    {
        $reflection = new \ReflectionClass(EventDispatcher::class);
        $property = $reflection->getProperty('listeners');
        $property->setAccessible(true);
        $property->setValue([]);
    }

    /**
     * Tests registering an event listener.
     */
    public function testAddListener()
    {
        $listener = function () {};
        EventDispatcher::addListener('test.event', $listener);

        $listeners = EventDispatcher::getListeners();

        $this->assertArrayHasKey('test.event', $listeners);
        $this->assertCount(1, $listeners['test.event']);
        $this->assertSame($listener, $listeners['test.event'][0]);
    }

    /**
     * Tests dispatching an event to a single listener.
     */
    public function testDispatchEventToListener()
    {
        $triggered = false;

        EventDispatcher::addListener('user.created', function ($data) use (&$triggered) {
            if ($data['username'] === 'john_doe') {
                $triggered = true;
            }
        });

        EventDispatcher::dispatch('user.created', ['username' => 'john_doe']);

        $this->assertTrue($triggered, 'Listener was not triggered.');
    }

    /**
     * Tests dispatching an event to multiple listeners.
     */
    public function testDispatchEventToMultipleListeners()
    {
        $result = [];

        EventDispatcher::addListener('order.completed', function ($data) use (&$result) {
            $result[] = 'Listener 1: ' . $data['orderId'];
        });

        EventDispatcher::addListener('order.completed', function ($data) use (&$result) {
            $result[] = 'Listener 2: ' . $data['orderId'];
        });

        EventDispatcher::dispatch('order.completed', ['orderId' => 123]);

        $this->assertCount(2, $result);
        $this->assertEquals('Listener 1: 123', $result[0]);
        $this->assertEquals('Listener 2: 123', $result[1]);
    }

    /**
     * Tests dispatching an event with no listeners.
     */
    public function testDispatchEventWithNoListeners()
    {
        // Expect no errors when dispatching an event with no listeners
        EventDispatcher::dispatch('nonexistent.event');

        $listeners = EventDispatcher::getListeners();

        $this->assertArrayNotHasKey('nonexistent.event', $listeners);
    }

    /**
     * Tests retrieving all registered listeners.
     */
    public function testGetListeners()
    {
        $listener1 = function () {};
        $listener2 = function () {};

        EventDispatcher::addListener('event.one', $listener1);
        EventDispatcher::addListener('event.two', $listener2);

        $listeners = EventDispatcher::getListeners();

        $this->assertArrayHasKey('event.one', $listeners);
        $this->assertArrayHasKey('event.two', $listeners);

        $this->assertSame($listener1, $listeners['event.one'][0]);
        $this->assertSame($listener2, $listeners['event.two'][0]);
    }
}
