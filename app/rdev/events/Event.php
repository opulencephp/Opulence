<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a base event class
 */
namespace RDev\Events;

abstract class Event implements IEvent
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