<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines different states of entities
 */
namespace RDev\ORM;

class EntityStates
{
    /** A new entity that isn't registered */
    const QUEUED = 1;
    /** An entity whose persistence is managed */
    const REGISTERED = 2;
    /** An entity that is no longer registered */
    const UNREGISTERED = 3;
    /** An entity that will be registered */
    const DEQUEUED = 4;
    /** An entity that was never registered */
    const NEVER_REGISTERED = 5;
} 