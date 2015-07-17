<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the event class
 */
namespace Opulence\Events;

class Event implements IEvent
{
    /** @var bool Whether or not the propagation has stopped */
    protected $propagationIsStopped = false;

    /**
     * {@inheritdoc}
     */
    public function propagationIsStopped()
    {
        return $this->propagationIsStopped;
    }

    /**
     * {@inheritdoc}
     */
    public function stopPropagation()
    {
        $this->propagationIsStopped = true;
    }
}