<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Events\Tests\Mocks;

use Opulence\Events\Dispatchers\IEventDispatcher;

/**
 * Mocks an event listener
 */
class Listener
{
    /**
     * Mocks a listen function that does nothing
     *
     * @param object $event The event to handle
     * @param string $eventName The name of the event
     * @param IEventDispatcher $dispatcher The event dispatcher
     */
    public function doNothing1($event, $eventName, IEventDispatcher $dispatcher)
    {
        // Don't do anything
    }

    /**
     * Mocks a listen function that does nothing
     *
     * @param object $event The event to handle
     * @param string $eventName The name of the event
     * @param IEventDispatcher $dispatcher The event dispatcher
     */
    public function doNothing2($event, $eventName, IEventDispatcher $dispatcher)
    {
        // Don't do anything
    }
}
