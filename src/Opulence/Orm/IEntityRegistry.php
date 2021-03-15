<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Orm;

/**
 * Defines the interface for entity registry to implement
 */
interface IEntityRegistry
{
    /**
     * Clears all the contents of the registry
     * This should only be called through a unit of work
     */
    public function clear();

    /**
     * Clears all aggregate root child functions
     */
    public function clearAggregateRoots();

    /**
     * Deregisters an entity
     * This should only be called through a unit of work
     *
     * @param object $entity The entity to detach
     */
    public function deregisterEntity($entity);

    /**
     * Gets the object's class name
     *
     * @param mixed $object The object whose class name we want
     * @return string The object's class name
     */
    public function getClassName($object) : string;

    /**
     * Gets the list of all registered entities
     *
     * @return object[] The list of all registered entities
     */
    public function getEntities() : array;

    /**
     * Attempts to get a registered entity
     *
     * @param string $className The name of the class the entity belongs to
     * @param int|string $id The entity's Id
     * @return object|null The entity if it was found, otherwise null
     */
    public function getEntity(string $className, $id);

    /**
     * Gets the entity state for the input entity
     *
     * @param object $entity The entity to check
     * @return int The entity state
     */
    public function getEntityState($entity) : int;

    /**
     * Gets a unique hash Id for an object
     *
     * @param mixed $object The object whose hash we want
     * @return string The object hash Id
     */
    public function getObjectHashId($object) : string;

    /**
     * Gets whether or not an entity is registered
     *
     * @param object $entity The entity to check
     * @return bool True if the entity is registered, otherwise false
     */
    public function isRegistered($entity) : bool;

    /**
     * Registers a function to set the aggregate root Id in a child entity after the aggregate root has been inserted
     * Since the child depends on the aggregate root's Id being set, make sure the root is inserted before the child
     *
     * @param object $aggregateRoot The aggregate root
     * @param object $child The child of the aggregate root
     * @param callable $function The function that contains the logic to set the aggregate root Id in the child
     */
    public function registerAggregateRootCallback($aggregateRoot, $child, callable $function);

    /**
     * Registers an entity
     *
     * @param object $entity The entity to register
     * @throws OrmException Thrown if there was an error registering the entity
     */
    public function registerEntity(&$entity);

    /**
     * Runs any aggregate root child functions registered for the entity
     *
     * @param object $child The child whose aggregate root functions we're running
     */
    public function runAggregateRootCallbacks($child);

    /**
     * Sets an entity's state
     *
     * @param object $entity The entity whose state we're setting
     * @param int $entityState The entity state
     */
    public function setState($entity, int $entityState);
}
