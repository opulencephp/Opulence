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
 * Defines the event class
 */
class Event implements IEvent
{
    /** @var bool Whether or not the propagation has stopped */
    protected $propagationIsStopped = false;

    /**
     * @inheritdoc
     */
    public function propagationIsStopped()
    {
        return $this->propagationIsStopped;
    }

    /**
     * @inheritdoc
     */
    public function stopPropagation()
    {
        $this->propagationIsStopped = true;
    }
}