<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines an entity registry
 */
namespace Opulence\ORM;

use ReflectionClass;

class EntityRegistry implements IEntityRegistry
{
    /** @var object[] The mapping of object Ids to their original data */
    protected $objectHashIdsToOriginalData = [];
    /**
     * The mapping of class names to comparison functions
     * Each function should return true if the entities are the same, otherwise false
     *
     * @var callable[]
     */
    protected $comparisonFunctions = [];
    /**
     * The mapping of class names to their getter and setter functions
     *
     * @var callable[]
     */
    protected $idAccessorFunctions = [];
    /** @var array The mapping of entities' object hash Ids to their various states */
    private $entityStates = [];
    /** @var array The mapping of class names to a list of entities of that class */
    private $entities = [];

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $this->objectHashIdsToOriginalData = [];
        $this->entities = [];
        $this->entityStates = [];
    }

    /**
     * @inheritdoc
     */
    public function deregisterEntity($entity)
    {
        $entityState = $this->getEntityState($entity);

        if ($entityState == EntityStates::QUEUED || $entityState == EntityStates::REGISTERED) {
            $className = $this->getClassName($entity);
            $objectHashId = $this->getObjectHashId($entity);
            $entityId = $this->getEntityId($entity);
            $this->entityStates[$objectHashId] = EntityStates::UNREGISTERED;
            unset($this->entities[$className][$entityId]);
            unset($this->objectHashIdsToOriginalData[$objectHashId]);
        }
    }

    /**
     * @inheritdoc
     */
    public function getClassName($object)
    {
        return get_class($object);
    }

    /**
     * @inheritdoc
     */
    public function getEntities()
    {
        if (count($this->entities) == 0) {
            return [];
        }

        // Flatten the  list of entities
        $entities = [];
        array_walk_recursive($this->entities, function ($entity) use (&$entities) {
            $entities[] = $entity;
        });

        return $entities;
    }

    /**
     * @inheritdoc
     */
    public function getEntity($className, $id)
    {
        if (!isset($this->entities[$className]) || !isset($this->entities[$className][$id])) {
            return null;
        }

        return $this->entities[$className][$id];
    }

    /**
     * Gets the Id of an entity
     *
     * @param object $entity The entity whose Id we want
     * @return mixed The Id of the entity
     * @throws ORMException Throw if no Id getter is registered for the entity
     */
    public function getEntityId($entity)
    {
        $className = $this->getClassName($entity);

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
    public function getEntityState($entity)
    {
        $objectHashId = $this->getObjectHashId($entity);

        if (!isset($this->entityStates[$objectHashId])) {
            return EntityStates::NEVER_REGISTERED;
        }

        return $this->entityStates[$objectHashId];
    }

    /**
     * @inheritdoc
     */
    public function getObjectHashId($object)
    {
        return spl_object_hash($object);
    }

    /**
     * @inheritdoc
     */
    public function hasChanged($entity)
    {
        if (!isset($this->objectHashIdsToOriginalData[$this->getObjectHashId($entity)])) {
            throw new ORMException("Entity is not registered");
        }

        // If a comparison function was specified, we don't bother using reflection to check for updates
        if (isset($this->comparisonFunctions[$this->getClassName($entity)])) {
            if ($this->hasChangedUsingComparisonFunction($entity)) {
                return true;
            }
        } elseif ($this->hasChangedUsingReflection($entity)) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function isRegistered($entity)
    {
        try {
            $entityId = $this->getEntityId($entity);

            return $this->getEntityState($entity) == EntityStates::REGISTERED
            || isset($this->entities[$this->getClassName($entity)][$entityId]);
        } catch (ORMException $ex) {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function registerComparisonFunction($className, callable $function)
    {
        $this->comparisonFunctions[$className] = $function;
    }

    /**
     * @inheritdoc
     */
    public function registerEntity(&$entity)
    {
        $className = $this->getClassName($entity);
        $objectHashId = $this->getObjectHashId($entity);
        $entityId = $this->getEntityId($entity);

        if (!isset($this->entities[$className])) {
            $this->entities[$className] = [];
        }

        if (isset($this->entities[$className][$entityId])) {
            // Change the reference of the input entity to the one that's already registered
            $entity = $this->getEntity($this->getClassName($entity), $entityId);
        } else {
            // Register this entity
            $this->objectHashIdsToOriginalData[$objectHashId] = clone $entity;
            $this->entities[$className][$entityId] = $entity;
            $this->entityStates[$objectHashId] = EntityStates::REGISTERED;
        }
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
        $className = $this->getClassName($entity);

        if (
            !isset($this->idAccessorFunctions[$className]["setter"]) ||
            $this->idAccessorFunctions[$className]["setter"] == null
        ) {
            throw new ORMException("No Id setter registered for class $className");
        }

        call_user_func($this->idAccessorFunctions[$className]["setter"], $entity, $id);
    }

    /**
     * @inheritdoc
     */
    public function setState($entity, $entityState)
    {
        $this->entityStates[$this->getObjectHashId($entity)] = $entityState;
    }

    /**
     * Checks to see if an entity has changed using a comparison function
     *
     * @param object $entity The entity to check for changes
     * @return bool True if the entity has changed, otherwise false
     */
    private function hasChangedUsingComparisonFunction($entity)
    {
        $objectHashId = $this->getObjectHashId($entity);
        $originalData = $this->objectHashIdsToOriginalData[$objectHashId];

        return !$this->comparisonFunctions[$this->getClassName($entity)]($originalData, $entity);
    }

    /**
     * Checks to see if an entity has changed using reflection
     *
     * @param object $entity The entity to check for changes
     * @return bool True if the entity has changed, otherwise false
     */
    private function hasChangedUsingReflection($entity)
    {
        // Get all the properties in the original entity and the current one
        $objectHashId = $this->getObjectHashId($entity);
        $currentEntityReflection = new ReflectionClass($entity);
        $currentProperties = $currentEntityReflection->getProperties();
        $currentPropertiesAsHash = [];
        $originalData = $this->objectHashIdsToOriginalData[$objectHashId];
        $originalEntityReflection = new ReflectionClass($originalData);
        $originalProperties = $originalEntityReflection->getProperties();
        $originalPropertiesAsHash = [];

        // Map each property name to its value for the current entity
        foreach ($currentProperties as $currentProperty) {
            $currentProperty->setAccessible(true);
            $currentPropertiesAsHash[$currentProperty->getName()] = $currentProperty->getValue($entity);
        }

        // Map each property name to its value for the original entity
        foreach ($originalProperties as $originalProperty) {
            $originalProperty->setAccessible(true);
            $originalPropertiesAsHash[$originalProperty->getName()] = $originalProperty->getValue($originalData);
        }

        if (count($originalProperties) != count($currentProperties)) {
            // Clearly there's a difference here, so update
            return true;
        }

        // Compare all the property values to see if they are identical
        foreach ($originalPropertiesAsHash as $name => $value) {
            if (!array_key_exists($name, $currentPropertiesAsHash) || $currentPropertiesAsHash[$name] !== $value) {
                return true;
            }
        }

        return false;
    }
}