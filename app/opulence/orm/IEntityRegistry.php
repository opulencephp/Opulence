<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for entity registry to implement
 */
namespace Opulence\ORM;

interface IEntityRegistry
{
    /**
     * Clears all the contents of the registry
     * All entities will appear
     * This should only be called through a unit of work
     */
    public function clear();

    /**
     * Deregisters an entity
     * This should only be called through a unit of work
     *
     * @param IEntity $entity The entity to detach
     */
    public function deregisterEntity(IEntity $entity);

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
     * @return IEntity[] The list of all registered entities
     */
    public function getEntities();

    /**
     * Attempts to get a registered entity
     *
     * @param string $className The name of the class the entity belongs to
     * @param int|string $id The entity's Id
     * @return IEntity|null The entity if it was found, otherwise null
     */
    public function getEntity($className, $id);

    /**
     * Gets the entity state for the input entity
     *
     * @param IEntity $entity The entity to check
     * @return int The entity state
     */
    public function getEntityState(IEntity $entity);

    /**
     * Gets a unique hash Id for an object
     *
     * @param mixed $object The object whose hash we want
     * @return string The object hash Id
     */
    public function getObjectHashId($object);

    /**
     * Checks whether or not an object has changed
     *
     * @param IEntity $entity The entity to check
     * @return bool True if the entity has changed, otherwise false
     * @throws ORMException Thrown if the entity was not registered
     */
    public function hasChanged(IEntity $entity);

    /**
     * Gets whether or not an entity is registered
     *
     * @param IEntity $entity The entity to check
     * @return bool True if the entity is registered, otherwise false
     */
    public function isRegistered(IEntity $entity);

    /**
     * Registers a comparison function for a class, which speeds up the check for updates
     * Registering a comparison function for a class will overwrite any previously-set comparison functions for that class
     *
     * @param string $className The name of the class whose comparison function we're registering
     * @param callable $function The function that takes two instances of the same class and returns whether or not
     *      they're considered identical
     */
    public function registerComparisonFunction($className, callable $function);

    /**
     * Registers an entity
     *
     * @param IEntity $entity The entity to register
     */
    public function registerEntity(IEntity &$entity);

    /**
     * Sets an entity's state
     *
     * @param IEntity $entity The entity whose state we're setting
     * @param int $entityState The entity state
     */
    public function setState(IEntity $entity, $entityState);
}