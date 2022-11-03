<?php

namespace Tmdb\Laravel\Adapters;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyDispatcher;

/**
 * This adapter provides a Laravel integration for applications
 * using the Symfony EventDispatcherInterface
 * It passes any request on to a Symfony Dispatcher and only
 * uses the Laravel Dispatcher when dispatching events
 */
abstract class EventDispatcherAdapter implements SymfonyDispatcher
{
    /**
     * The Laravel Events Dispatcher
     * @var \Illuminate\Contracts\Events\Dispatcher or \Illuminate\Events\Dispatcher
     */
    protected $laravelDispatcher;

    /**
     * The Symfony Event Dispatcher
     * @var  \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $symfonyDispatcher;

    /**
     * Dispatches an event to all registered listeners.
     *
     * @template T of object
     *
     * @param T $event The event to pass to the event handlers/listeners
     * @param string|null $eventName The name of the event to dispatch. If not supplied,
     *                               the class of $event should be used instead.
     *
     * @return T The passed $event MUST be returned
     */
    public function dispatch(object $event, string $eventName = null): object
    {
        $this->laravelDispatcher->dispatch($eventName, $event);

        return $this->symfonyDispatcher->dispatch($eventName, $event);
    }

    /**
     * Adds an event listener that listens on the specified events.
     *
     * @param string $eventName The event to listen on
     * @param callable $listener The listener
     * @param int $priority The higher this value, the earlier an event
     *                            listener will be triggered in the chain (defaults to 0)
     */
    public function addListener(string $eventName, callable $listener, int $priority = 0)
    {
        $this->symfonyDispatcher->addListener($eventName, $listener, $priority);
    }

    /**
     * Adds an event subscriber.
     *
     * The subscriber is asked for all the events it is
     * interested in and added as a listener for these events.
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->symfonyDispatcher->addSubscriber($subscriber);
    }

    /**
     * Removes an event listener from the specified events.
     *
     * @param string $eventName The event to remove a listener from
     * @param callable $listenerToBeRemoved The listener to remove
     */
    public function removeListener(string $eventName, callable $listener)
    {
        $this->symfonyDispatcher->removeListener($eventName, $listener);
    }

    /**
     * Removes an event subscriber.
     *
     * @param EventSubscriberInterface $subscriber The subscriber
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->symfonyDispatcher->removeSubscriber($subscriber);
    }

    /**
     * Gets the listeners of a specific event or all listeners sorted by descending priority.
     *
     * @return array<callable[]|callable>
     */
    public function getListeners(string $eventName = null): array
    {
        return $this->symfonyDispatcher->getListeners($eventName);
    }

    /**
     * Gets the listener priority for a specific event.
     *
     * Returns null if the event or the listener does not exist.
     *
     * @param string $eventName The name of the event
     * @param callable $listener The listener
     *
     * @return int|null The event listener priority
     */
    public function getListenerPriority(string $eventName, callable $listener): ?int
    {
        return $this->symfonyDispatcher->getListenerPriority($eventName, $listener);
    }

    /**
     * Checks whether an event has any registered listeners.
     *
     * @param string|null $eventName The name of the event
     *
     * @return bool true if the specified event has any listeners, false otherwise
     */
    public function hasListeners(string $eventName = null): bool
    {
        return ($this->symfonyDispatcher->hasListeners($eventName) ||
            $this->laravelDispatcher->hasListeners($eventName));
    }
}
