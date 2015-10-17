<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for Id accessor registries to implement
 */
namespace Opulence\ORM\Ids;

use Opulence\ORM\ORMException;

interface IIdAccessorRegistry
{
    /**
     * Gets the Id of an entity
     *
     * @param object $entity The entity whose Id we want
     * @return mixed The Id of the entity
     * @throws ORMException Throw if no Id getter is registered for the entity
     */
    public function getEntityId($entity);

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
}