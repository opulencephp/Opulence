<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Events\Dispatchers;

/**
 * Defines the event registry
 */
class EventRegistry implements IEventRegistry
{
    /** @var array The mapping of event names to the list of listener */
    private $eventNamesToListeners = [];

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
