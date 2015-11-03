<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Events;

/**
 * Defines the interface for events to implement
 */
interface IEvent
{
    /**
     * Gets whether or not the propagation has been stopped for this event
     *
     * @return bool True if the propagation is stopped, otherwise false
     */
    public function propagationIsStopped();

    /**
     * Stops the propagation of this event
     */
    public function stopPropagation();
}