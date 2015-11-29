<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Orm\Ids\Accessors;

use Opulence\Orm\IEntity;
use Opulence\Orm\OrmException;

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

        if (
            !isset($this->idAccessorFunctions[$className]["getter"]) ||
            $this->idAccessorFunctions[$className]["getter"] == null
        ) {
            if (!$entity instanceof IEntity) {
                throw new OrmException("No Id getter registered for class $className");
            }

            $className = IEntity::class;
        }

        return call_user_func($this->idAccessorFunctions[$className]["getter"], $entity);
    }

    /**
     * @inheritdoc
     */
    public function registerIdAccessors($classNames, callable $getter, callable $setter = null)
    {
        foreach ((array)$classNames as $className) {
            $this->idAccessorFunctions[$className] = [
                "getter" => $getter,
                "setter" => $setter
            ];
        }
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entity, $id)
    {
        $className = get_class($entity);

        if (!isset($this->idAccessorFunctions[$className]["setter"])) {
            if (!$entity instanceof IEntity) {
                throw new OrmException("No Id setter registered for class $className");
            }

            $className = IEntity::class;
        }

        call_user_func($this->idAccessorFunctions[$className]["setter"], $entity, $id);
    }
}