<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Orm\Ids\Accessors;

use Opulence\Orm\OrmException;

/**
 * Defines the interface for Id accessor registries to implement
 */
interface IIdAccessorRegistry
{
    /**
     * Gets the Id of an entity
     *
     * @param object $entity The entity whose Id we want
     * @return mixed The Id of the entity
     * @throws OrmException Thrown if no Id getter is registered for the entity
     */
    public function getEntityId($entity);

    /**
     * Registers functions that get an Id and set the Id for all instances of the input class name
     *
     * @param string|array $classNames The name or list of names of classes whose Id getter functions we're registering
     * @param callable $getter The function that accepts an entity as a parameter and returns its Id
     * @param callable $setter The function that accepts an entity and new Id as parameters and sets the Id
     */
    public function registerIdAccessors($classNames, callable $getter, callable $setter = null);

    /**
     * Registers accessors that use reflection to set Id properties in the input classes
     *
     * @param string|array $classNames The name or list of names of classes whose Id accessors we're registering
     * @param string $idPropertyName The name of the Id property we're registering
     */
    public function registerReflectionIdAccessors($classNames, $idPropertyName);

    /**
     * Sets the entity Id
     *
     * @param object $entity The entity whose Id we're setting
     * @param mixed $id The Id to set
     * @throws OrmException Thrown if no Id setter has been registered for this entity
     */
    public function setEntityId($entity, $id);
}