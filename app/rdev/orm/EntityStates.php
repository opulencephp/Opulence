<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines different states of entities
 */
namespace RDev\ORM;

class EntityStates
{
    /** A new entity that isn't managed */
    const ADDED = 1;
    /** An entity whose persistence is managed */
    const MANAGED = 2;
    /** An entity that is no longer being managed */
    const DETACHED = 3;
    /** An entity that will be deleted */
    const DELETED = 4;
    /** An entity that was never managed */
    const UNMANAGED = 5;
} 