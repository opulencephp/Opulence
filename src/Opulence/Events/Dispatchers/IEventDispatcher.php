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
interface IEventDispatcher
{
    /**
     * Dispatches an event
     *
     * @param string $eventName The name of the event to dispatch
     * @param IEvent $event The event to dispatch
     */
    public function dispatch(string $eventName, IEvent $event);
}