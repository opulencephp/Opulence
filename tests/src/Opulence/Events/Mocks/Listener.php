<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Events\Mocks;

use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Events\IEvent;

/**
 * Mocks an event listener
 */
class Listener
{
    /**
     * Mocks a listen function that does nothing
     *
     * @param IEvent $event The event to handle
     * @param string $eventName The name of the event
     * @param IEventDispatcher $dispatcher The event dispatcher
     */
    public function doNothing1(IEvent $event, $eventName, IEventDispatcher $dispatcher)
    {
        // Don't do anything
    }

    /**
     * Mocks a listen function that does nothing
     *
     * @param IEvent $event The event to handle
     * @param string $eventName The name of the event
     * @param IEventDispatcher $dispatcher The event dispatcher
     */
    public function doNothing2(IEvent $event, $eventName, IEventDispatcher $dispatcher)
    {
        // Don't do anything
    }

    /**
     * Mocks a listen function that stops propagation
     *
     * @param IEvent $event The event to handle
     * @param string $eventName The name of the event
     * @param IEventDispatcher $dispatcher The event dispatcher
     */
    public function stopsPropagation(IEvent $event, $eventName, IEventDispatcher $dispatcher)
    {
        $event->stopPropagation();
    }
}