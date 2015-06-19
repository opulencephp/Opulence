<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for events to implement
 */
namespace RDev\Events;

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