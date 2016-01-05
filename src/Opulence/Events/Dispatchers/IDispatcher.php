<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Events\Dispatchers;

use Opulence\Events\IEvent;

/**
 * Defines the interface for event dispatchers to implement
 */
interface IDispatcher
{
    /**
     * Dispatches an event
     *
     * @param string $eventName The name of the event to dispatch
     * @param IEvent $event The event to dispatch
     */
    public function dispatch($eventName, IEvent $event);

    /**
     * Gets the list of listeners for an event name
     *
     * @param string $eventName The event whose listeners we want
     * @return callable[] The list of listeners for the event
     */
    public function getListeners($eventName);

    /**
     * Gets whether or not an event name has any listeners
     *
     * @param string $eventName The event name to look for
     * @return bool Whether or not the event name has listeners
     */
    public function hasListeners($eventName);

    /**
     * Adds a listener for an event
     *
     * @param string $eventName The name of the event the listener listens to
     * @param callable $listener The listener to add
     */
    public function registerListener($eventName, callable $listener);

    /**
     * Removes a listener from an event name
     *
     * @param string $eventName The event name to look for
     * @param callable $listener the listener to remove
     */
    public function removeListener($eventName, callable $listener);
}