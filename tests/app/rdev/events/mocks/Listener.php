<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks an event listener
 */
namespace RDev\Tests\Events\Mocks;
use RDev\Events\Dispatchers\IDispatcher;
use RDev\Events\IEvent;

class Listener
{
    /**
     * Mocks a listen function that does nothing
     *
     * @param IEvent $event The event to handle
     * @param string $eventName The name of the event
     * @param IDispatcher $dispatcher The event dispatcher
     */
    public function doNothing1(IEvent $event, $eventName, IDispatcher $dispatcher)
    {
        // Don't do anything
    }

    /**
     * Mocks a listen function that does nothing
     *
     * @param IEvent $event The event to handle
     * @param string $eventName The name of the event
     * @param IDispatcher $dispatcher The event dispatcher
     */
    public function doNothing2(IEvent $event, $eventName, IDispatcher $dispatcher)
    {
        // Don't do anything
    }

    /**
     * Mocks a listen function that stops propagation
     *
     * @param IEvent $event The event to handle
     * @param string $eventName The name of the event
     * @param IDispatcher $dispatcher The event dispatcher
     */
    public function stopsPropagation(IEvent $event, $eventName, IDispatcher $dispatcher)
    {
        $event->stopPropagation();
    }
}