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
 * Defines the event dispatcher
 */
class Dispatcher implements IDispatcher
{
    /** @var array The mapping of event names to the list of listener */
    protected $eventNamesToListeners = [];

    /**
     * @inheritdoc
     */
    public function dispatch(string $eventName, IEvent $event)
    {
        if (isset($this->eventNamesToListeners[$eventName])) {
            foreach ($this->eventNamesToListeners[$eventName] as $listener) {
                call_user_func($listener, $event, $eventName, $this);

                if ($event->propagationIsStopped()) {
                    break;
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getListeners(string $eventName) : array
    {
        if (!isset($this->eventNamesToListeners[$eventName])) {
            return [];
        }

        return $this->eventNamesToListeners[$eventName];
    }

    /**
     * @inheritdoc
     */
    public function hasListeners(string $eventName) : bool
    {
        return isset($this->eventNamesToListeners[$eventName]) && count($this->eventNamesToListeners[$eventName]) > 0;
    }

    /**
     * @inheritdoc
     */
    public function registerListener(string $eventName, callable $listener)
    {
        if (!isset($this->eventNamesToListeners[$eventName])) {
            $this->eventNamesToListeners[$eventName] = [];
        }

        if (!in_array($listener, $this->eventNamesToListeners[$eventName])) {
            $this->eventNamesToListeners[$eventName][] = $listener;
        }
    }

    /**
     * @inheritdoc
     */
    public function removeListener(string $eventName, callable $listener)
    {
        if (
            isset($this->eventNamesToListeners[$eventName]) &&
            ($index = array_search($listener, $this->eventNamesToListeners[$eventName])) !== false
        ) {
            unset($this->eventNamesToListeners[$eventName][$index]);
        }
    }
}