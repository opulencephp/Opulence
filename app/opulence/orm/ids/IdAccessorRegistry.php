<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the Id accessor registry
 */
namespace Opulence\ORM\Ids;

use Opulence\ORM\ORMException;

class IdAccessorRegistry implements IIdAccessorRegistry
{
    /** @var callable[] The mapping of class names to their getter and setter functions */
    protected $idAccessorFunctions = [];

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
            throw new ORMException("No Id getter registered for class $className");
        }

        return call_user_func($this->idAccessorFunctions[$className]["getter"], $entity);
    }

    /**
     * @inheritdoc
     */
    public function registerIdAccessors($className, callable $getter, callable $setter = null)
    {
        $this->idAccessorFunctions[$className] = [
            "getter" => $getter,
            "setter" => $setter
        ];
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entity, $id)
    {
        $className = get_class($entity);

        if (
            !isset($this->idAccessorFunctions[$className]["setter"]) ||
            $this->idAccessorFunctions[$className]["setter"] == null
        ) {
            throw new ORMException("No Id setter registered for class $className");
        }

        call_user_func($this->idAccessorFunctions[$className]["setter"], $entity, $id);
    }
}