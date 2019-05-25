<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

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
    public function doNothing1(object $event, $eventName, IEventDispatcher $dispatcher)
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
    public function doNothing2(object $event, $eventName, IEventDispatcher $dispatcher)
    {
        // Don't do anything
    }
}
