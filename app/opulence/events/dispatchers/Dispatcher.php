<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the event dispatcher
 */
namespace Opulence\Events\Dispatchers;
use Opulence\Events\IEvent;

class Dispatcher implements IDispatcher
{
    /** @var array The mapping of event names to the list of listener */
    protected $eventNamesToListeners = [];

    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, IEvent $event)
    {
        if(isset($this->eventNamesToListeners[$eventName]))
        {
            foreach($this->eventNamesToListeners[$eventName] as $listener)
            {
                call_user_func($listener, $event, $eventName, $this);

                if($event->propagationIsStopped())
                {
                    break;
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getListeners($eventName)
    {
        if(!isset($this->eventNamesToListeners[$eventName]))
        {
            return [];
        }

        return $this->eventNamesToListeners[$eventName];
    }

    /**
     * {@inheritdoc}
     */
    public function hasListeners($eventName)
    {
        return isset($this->eventNamesToListeners[$eventName]) && count($this->eventNamesToListeners[$eventName]) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function registerListener($eventName, callable $listener)
    {
        if(!isset($this->eventNamesToListeners[$eventName]))
        {
            $this->eventNamesToListeners[$eventName] = [];
        }

        if(!in_array($listener, $this->eventNamesToListeners[$eventName]))
        {
            $this->eventNamesToListeners[$eventName][] = $listener;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeListener($eventName, callable $listener)
    {
        if(
            isset($this->eventNamesToListeners[$eventName]) &&
            ($index = array_search($listener, $this->eventNamesToListeners[$eventName])) !== false
        )
        {
            unset($this->eventNamesToListeners[$eventName][$index]);
        }
    }
}