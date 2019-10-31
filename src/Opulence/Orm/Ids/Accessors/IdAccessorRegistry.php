<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Orm\Ids\Accessors;

use Opulence\Orm\IEntity;
use Opulence\Orm\OrmException;
use ReflectionClass;
use ReflectionException;

/**
 * Defines the Id accessor registry
 */
class IdAccessorRegistry implements IIdAccessorRegistry
{
    /** @var callable[] The mapping of class names to their getter and setter functions */
    protected $idAccessorFunctions = [];

    public function __construct()
    {
        /**
         * To reduce boilerplate code, users can implement the entity interface
         * We'll automatically register Id accessors for classes that implement this interface
         */
        $this->registerIdAccessors(
            IEntity::class,
            function ($entity) {
                /** @var IEntity $entity */
                return $entity->getId();
            },
            function ($entity, $id) {
                /** @var IEntity $entity */
                $entity->setId($id);
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function getEntityId($entity)
    {
        $className = get_class($entity);

        if (!isset($this->idAccessorFunctions[$className]['getter'])) {
            if (!$entity instanceof IEntity) {
                throw new OrmException("No Id getter registered for class $className");
            }

            $className = IEntity::class;
        }

        try {
            return $this->idAccessorFunctions[$className]['getter']($entity);
        } catch (ReflectionException $ex) {
            throw new OrmException('Failed to get entity Id', 0, $ex);
        }
    }

    /**
     * @inheritdoc
     */
    public function registerIdAccessors($classNames, callable $getter, callable $setter = null)
    {
        foreach ((array)$classNames as $className) {
            $this->idAccessorFunctions[$className] = [
                'getter' => $getter,
                'setter' => $setter
            ];
        }
    }

    /**
     * @inheritdoc
     */
    public function registerReflectionIdAccessors($classNames, string $idPropertyName)
    {
        foreach ((array)$classNames as $className) {
            try {
                $reflectionClass = new ReflectionClass($className);
                $property = $reflectionClass->getProperty($idPropertyName);
                $property->setAccessible(true);
            } catch (ReflectionException $ex) {
                throw new OrmException("Reflection failed for Id accessors in class \"$className\"");
            }

            $getter = function ($entity) use ($property) {
                return $property->getValue($entity);
            };
            $setter = function ($entity, $id) use ($property) {
                $property->setValue($entity, $id);
            };
            $this->registerIdAccessors($className, $getter, $setter);
        }
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entity, $id)
    {
        $className = get_class($entity);

        if (!isset($this->idAccessorFunctions[$className]['setter'])) {
            if (!$entity instanceof IEntity) {
                throw new OrmException("No Id setter registered for class $className");
            }

            $className = IEntity::class;
        }

        try {
            $this->idAccessorFunctions[$className]['setter']($entity, $id);
        } catch (ReflectionException $ex) {
            throw new OrmException('Failed to set entity Id', 0, $ex);
        }
    }
}
