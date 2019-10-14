<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Orm\Ids\Accessors;

use Opulence\Orm\IEntity;
use Opulence\Orm\OrmException;
use ReflectionClass;
use ReflectionException;

/**
 * Defines the Id accessor registry
 */
final class IdAccessorRegistry implements IIdAccessorRegistry
{
    /** @var callable[] The mapping of class names to their getter and setter functions */
    protected array $idAccessorFunctions = [];

    public function __construct()
    {
        /**
         * To reduce boilerplate code, users can implement the entity interface
         * We'll automatically register Id accessors for classes that implement this interface
         */
        $this->registerIdAccessors(
            IEntity::class,
            fn (IEntity $entity) => $entity->getId(),
            fn (IEntity $entity, $id) => $entity->setId($id)
        );
    }

    /**
     * @inheritdoc
     */
    public function getEntityId(object $entity)
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
    public function registerIdAccessors($classNames, callable $getter, callable $setter = null): void
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
    public function registerReflectionIdAccessors($classNames, string $idPropertyName): void
    {
        foreach ((array)$classNames as $className) {
            try {
                $reflectionClass = new ReflectionClass($className);
                $property = $reflectionClass->getProperty($idPropertyName);
                $property->setAccessible(true);
            } catch (ReflectionException $ex) {
                throw new OrmException("Reflection failed for Id accessors in class \"$className\"");
            }

            $getter = fn ($entity) => $property->getValue($entity);
            $setter = fn ($entity, $id) => $property->setValue($entity, $id);
            $this->registerIdAccessors($className, $getter, $setter);
        }
    }

    /**
     * @inheritdoc
     */
    public function setEntityId(object $entity, $id): void
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
