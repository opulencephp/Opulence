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
     * Gets the entity Id
     *
     * @param object $entity The entity whose Id we want
     * @return mixed The Id
     * @throws ORMException Thrown if no Id getter has been registered for this entity
     */
    public function getEntityId($entity);

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
     * Checks whether or not an object has changed
     *
     * @param object $entity The entity to check
     * @return bool True if the entity has changed, otherwise false
     * @throws ORMException Thrown if the entity was not registered
     */
    public function hasChanged($entity);

    /**
     * Gets whether or not an entity is registered
     *
     * @param object $entity The entity to check
     * @return bool True if the entity is registered, otherwise false
     */
    public function isRegistered($entity);

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
     * @param object $entity The entity to register
     * @throws ORMException Thrown if there was an error registering the entity
     */
    public function registerEntity(&$entity);

    /**
     * Registers functions that get an Id and set the Id for all instances of the input class name
     *
     * @param string $className The name of the class whose Id getter function we're registering
     * @param callable $getter The function that accepts an entity as a parameter and returns its Id
     * @param callable $setter The function that accepts an entity and new Id as parameters and sets the Id
     */
    public function registerIdAccessors($className, callable $getter, callable $setter = null);

    /**
     * Sets the entity Id
     *
     * @param object $entity The entity whose Id we're setting
     * @param mixed $id The Id to set
     * @throws ORMException Thrown if no Id setter has been registered for this entity
     */
    public function setEntityId($entity, $id);

    /**
     * Sets an entity's state
     *
     * @param object $entity The entity whose state we're setting
     * @param int $entityState The entity state
     */
    public function setState($entity, $entityState);
}