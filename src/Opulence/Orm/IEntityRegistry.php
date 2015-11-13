<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
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
    public function getClassName($object);

    /**
     * Gets the list of all registered entities
     *
     * @return object[] The list of all registered entities
     */
    public function getEntities();

    /**
     * Attempts to get a registered entity
     *
     * @param string $className The name of the class the entity belongs to
     * @param int|string $id The entity's Id
     * @return object|null The entity if it was found, otherwise null
     */
    public function getEntity($className, $id);

    /**
     * Gets the entity state for the input entity
     *
     * @param object $entity The entity to check
     * @return int The entity state
     */
    public function getEntityState($entity);

    /**
     * Gets a unique hash Id for an object
     *
     * @param mixed $object The object whose hash we want
     * @return string The object hash Id
     */
    public function getObjectHashId($object);

    /**
     * Gets whether or not an entity is registered
     *
     * @param object $entity The entity to check
     * @return bool True if the entity is registered, otherwise false
     */
    public function isRegistered($entity);

    /**
     * Registers an entity
     *
     * @param object $entity The entity to register
     * @throws OrmException Thrown if there was an error registering the entity
     */
    public function registerEntity(&$entity);

    /**
     * Sets an entity's state
     *
     * @param object $entity The entity whose state we're setting
     * @param int $entityState The entity state
     */
    public function setState($entity, $entityState);
}