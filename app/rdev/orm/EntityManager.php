<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Defines an entity manager
 */
namespace RDev\ORM;

class EntityManager implements IEntityManager
{
    /** @var IEntity[] The mapping of object Ids to their original data */
    protected $objectHashIdsToOriginalData = [];
    /**
     * The mapping of class names to comparison functions
     * Each function should return true if the entities are the same, otherwise false
     *
     * @var callable[]
     */
    protected $comparisonFunctions = [];
    /** @var array The mapping of entities' object hash Ids to their various states */
    private $entityStates = [];
    /** @var array The mapping of class names to a list of entities of that class */
    private $managedEntities = [];

    /**
     * {@inheritdoc}
     */
    public function detach(IEntity $entity)
    {
        $entityState = $this->getEntityState($entity);

        if($entityState == EntityStates::ADDED || $entityState == EntityStates::MANAGED)
        {
            $className = $this->getClassName($entity);
            $objectHashId = $this->getObjectHashId($entity);
            $this->entityStates[$objectHashId] = EntityStates::DETACHED;
            unset($this->managedEntities[$className][$entity->getId()]);
            unset($this->objectHashIdsToOriginalData[$objectHashId]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dispose()
    {
        $this->objectHashIdsToOriginalData = [];
        $this->managedEntities = [];
        $this->entityStates = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getClassName($object)
    {
        return get_class($object);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityState(IEntity $entity)
    {
        $objectHashId = $this->getObjectHashId($entity);

        if(!isset($this->entityStates[$objectHashId]))
        {
            return EntityStates::UNMANAGED;
        }

        return $this->entityStates[$objectHashId];
    }

    /**
     * {@inheritdoc}
     */
    public function getManagedEntities()
    {
        if(count($this->managedEntities) == 0)
        {
            return [];
        }

        // Flatten the  list of entities
        $entities = [];
        array_walk_recursive($this->managedEntities, function($entity) use (&$entities)
        {
            $entities[] = $entity;
        });

        return $entities;
    }

    /**
     * {@inheritdoc}
     */
    public function getManagedEntity($className, $id)
    {
        if(!isset($this->managedEntities[$className]) || !isset($this->managedEntities[$className][$id]))
        {
            return null;
        }

        return $this->managedEntities[$className][$id];
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectHashId($object)
    {
        return spl_object_hash($object);
    }

    /**
     * {@inheritdoc}
     */
    public function hasChanged(IEntity $entity)
    {
        if(!isset($this->objectHashIdsToOriginalData[$this->getObjectHashId($entity)]))
        {
            throw new ORMException("Entity is not being tracked");
        }

        // If a comparison function was specified, we don't bother using reflection to check for updates
        if(isset($this->comparisonFunctions[$this->getClassName($entity)]))
        {
            if($this->checkEntityForUpdatesWithComparisonFunction($entity))
            {
                return true;
            }
        }
        elseif($this->checkEntityForUpdatesWithReflection($entity))
        {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isManaged(IEntity $entity)
    {
        return $this->getEntityState($entity) == EntityStates::MANAGED
        || isset($this->managedEntities[$this->getClassName($entity)][$entity->getId()]);
    }

    /**
     * {@inheritdoc}
     */
    public function manage(IEntity &$entity)
    {
        $className = $this->getClassName($entity);
        $objectHashId = $this->getObjectHashId($entity);

        if(!isset($this->managedEntities[$className]))
        {
            $this->managedEntities[$className] = [];
        }

        if(isset($this->managedEntities[$className][$entity->getId()]))
        {
            // Change the reference of the input entity to the one that's already managed
            $entity = $this->getManagedEntity($this->getClassName($entity), $entity->getId());
        }
        else
        {
            // Manage this entity
            $this->objectHashIdsToOriginalData[$objectHashId] = clone $entity;
            $this->managedEntities[$className][$entity->getId()] = $entity;
            $this->entityStates[$objectHashId] = EntityStates::MANAGED;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function registerComparisonFunction($className, callable $function)
    {
        $this->comparisonFunctions[$className] = $function;
    }

    /**
     * {@inheritdoc}
     */
    public function setState(IEntity $entity, $entityState)
    {
        $this->entityStates[$this->getObjectHashId($entity)] = $entityState;
    }

    /**
     * Checks to see if an entity has changed using a comparison function
     *
     * @param IEntity $entity The entity to check for changes
     * @return bool True if the entity has changed, otherwise false
     */
    private function checkEntityForUpdatesWithComparisonFunction(IEntity $entity)
    {
        $objectHashId = $this->getObjectHashId($entity);
        $originalData = $this->objectHashIdsToOriginalData[$objectHashId];

        return !$this->comparisonFunctions[$this->getClassName($entity)]($originalData, $entity);
    }

    /**
     * Checks to see if an entity has changed using reflection
     *
     * @param IEntity $entity The entity to check for changes
     * @return bool True if the entity has changed, otherwise false
     */
    private function checkEntityForUpdatesWithReflection(IEntity $entity)
    {
        // Get all the properties in the original entity and the current one
        $objectHashId = $this->getObjectHashId($entity);
        $currentEntityReflection = new \ReflectionClass($entity);
        $currentProperties = $currentEntityReflection->getProperties();
        $currentPropertiesAsHash = [];
        $originalData = $this->objectHashIdsToOriginalData[$objectHashId];
        $originalEntityReflection = new \ReflectionClass($originalData);
        $originalProperties = $originalEntityReflection->getProperties();
        $originalPropertiesAsHash = [];

        // Map each property name to its value for the current entity
        foreach($currentProperties as $currentProperty)
        {
            $currentProperty->setAccessible(true);
            $currentPropertiesAsHash[$currentProperty->getName()] = $currentProperty->getValue($entity);
        }

        // Map each property name to its value for the original entity
        foreach($originalProperties as $originalProperty)
        {
            $originalProperty->setAccessible(true);
            $originalPropertiesAsHash[$originalProperty->getName()] = $originalProperty->getValue($originalData);
        }

        if(count($originalProperties) != count($currentProperties))
        {
            // Clearly there's a difference here, so update
            return true;
        }

        // Compare all the property values to see if they are identical
        foreach($originalPropertiesAsHash as $name => $value)
        {
            if(!array_key_exists($name, $currentPropertiesAsHash) || $currentPropertiesAsHash[$name] !== $value)
            {
                return true;
            }
        }

        return false;
    }
}