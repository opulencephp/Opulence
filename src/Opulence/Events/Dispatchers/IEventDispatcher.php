<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Events\Dispatchers;

/**
 * Defines the interface for event dispatchers to implement
 */
interface IEventDispatcher
{
    /**
     * Dispatches an event
     *
     * @param string $eventName The name of the event to dispatch
     * @param object $event The event to dispatch
     */
    public function dispatch(string $eventName, $event);
}
