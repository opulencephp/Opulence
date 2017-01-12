<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Orm\ChangeTracking;

use Opulence\Orm\OrmException;

/**
 * Defines the interface for change trackers to implement
 */
interface IChangeTracker
{
    /**
     * Gets whether or not an entity has changed since it was registered
     *
     * @param object $entity The entity to check
     * @return bool True if the entity has changed, otherwise false
     * @throws OrmException Thrown if the entity was not registered in the first place
     */
    public function hasChanged($entity) : bool;

    /**
     * Registers a function that compares two entities and determines whether or not they're the same
     *
     * @param string $className The name of the class whose comparator we're registering
     * @param callable $comparator The function that accepts two entities and returns whether or not they're the same
     */
    public function registerComparator(string $className, callable $comparator);

    /**
     * Starts tracking an entity
     *
     * @param object $entity The entity to start tracking
     */
    public function startTracking($entity);

    /**
     * Stops tracking an entity
     *
     * @param object $entity The entity to deregister
     */
    public function stopTracking($entity);

    /**
     * Stops tracking all entities
     */
    public function stopTrackingAll();
}
