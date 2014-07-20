<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a unit of work that tracks changes made to entities and atomically persists them
 */
namespace RDev\Models\ORM;
use RDev\Models;
use RDev\Models\Databases\SQL;
use RDev\Models\Exceptions;
use RDev\Models\ORM\DataMappers;
use RDev\Models\ORM\Exceptions as ORMExceptions;

class UnitOfWork
{
    /** @var SQL\IConnection The connection to use in our unit of work */
    private $connection = null;
    /** @var array The mapping of class names to their data mappers */
    private $dataMappers = [];
    /** @var array The list of entities scheduled for insertion */
    private $scheduledForInsertion = [];
    /** @var array The list of entities scheduled for update */
    private $scheduledForUpdate = [];
    /** @var array The list of entities scheduled for deletion */
    private $scheduledForDeletion = [];
    /** @var array The mapping of object Ids to their original data */
    private $objectHashIdsToOriginalData = [];
    /** @var array The mapping of entities' object hash Ids to their various states */
    private $entityStates = [];
    /** @var array The mapping of class names to a list of entities of that class */
    private $managedEntities = [];

    /**
     * @param SQL\IConnection $connection The connection to use in our unit of work
     */
    public function __construct(SQL\IConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Commits any entities that have been scheduled for insertion/updating/deletion
     *
     * @throws ORMExceptions\ORMException Thrown if there was an error committing the transaction
     */
    public function commit()
    {
        $this->checkForUpdates();
        $this->connection->beginTransaction();

        try
        {
            $this->insert();
            $this->update();
            $this->delete();
            $this->connection->commit();
        }
        catch(\Exception $ex)
        {
            Exceptions\Log::write("Failed to commit: " . $ex);
            $this->connection->rollBack();
            $this->postRollback();
            throw new ORMExceptions\ORMException($ex->getMessage());
        }

        $this->postCommit();

        // Clear our schedules
        $this->scheduledForInsertion = [];
        $this->scheduledForUpdate = [];
        $this->scheduledForDeletion = [];
    }

    /**
     * Detaches an entity from being managed
     *
     * @param Models\IEntity $entity The entity to detach
     */
    public function detach(Models\IEntity $entity)
    {
        $entityState = $this->getEntityState($entity);

        if($entityState == EntityStates::ADDED || $entityState == EntityStates::MANAGED)
        {
            $className = get_class($entity);
            $objectHashId = $this->getObjectHashId($entity);
            $this->entityStates[$objectHashId] = EntityStates::DETACHED;
            unset($this->managedEntities[$className][$entity->getId()]);
            unset($this->objectHashIdsToOriginalData[$objectHashId]);
            unset($this->scheduledForInsertion[$objectHashId]);
            unset($this->scheduledForUpdate[$objectHashId]);
            unset($this->scheduledForDeletion[$objectHashId]);
        }
    }

    /**
     * Disposes of all data in this unit of work
     */
    public function dispose()
    {
        $this->scheduledForInsertion = [];
        $this->scheduledForUpdate = [];
        $this->scheduledForDeletion = [];
        $this->managedEntities = [];
        $this->entityStates = [];
        $this->objectHashIdsToOriginalData = [];
    }

    /**
     * Gets the data mapper for the input class
     *
     * @param string $className The name of the class whose data mapper we're searching for
     * @return DataMappers\IDataMapper The data mapper for the input class
     * @throws \RuntimeException Thrown if there was no data mapper for the input class name
     */
    public function getDataMapper($className)
    {
        if(!isset($this->dataMappers[$className]))
        {
            throw new \RuntimeException("No data mapper for " . $className);
        }

        return $this->dataMappers[$className];
    }

    /**
     * Gets the entity state for the input entity
     *
     * @param Models\IEntity $entity The entity to check
     * @return int The entity state
     */
    public function getEntityState(Models\IEntity $entity)
    {
        $objectHashId = $this->getObjectHashId($entity);

        if(!isset($this->entityStates[$objectHashId]))
        {
            return EntityStates::UNMANAGED;
        }

        return $this->entityStates[$objectHashId];
    }

    /**
     * Attempts to get a managed entity
     *
     * @param string $className The name of the class the entity belongs to
     * @param int|string $id The entity's Id
     * @return Models\IEntity|bool The entity if it was found, otherwise false
     */
    public function getManagedEntity($className, $id)
    {
        if(!isset($this->managedEntities[$className]) || !isset($this->managedEntities[$className][$id]))
        {
            return false;
        }

        return $this->managedEntities[$className][$id];
    }

    /**
     * Gets the list of entities that are scheduled for deletion
     *
     * @return Models\IEntity[] The list of entities scheduled for deletion
     */
    public function getScheduledEntityDeletions()
    {
        return array_values($this->scheduledForDeletion);
    }

    /**
     * Gets the list of entities that are scheduled for insertion
     *
     * @return Models\IEntity[] The list of entities scheduled for insertion
     */
    public function getScheduledEntityInsertions()
    {
        return array_values($this->scheduledForInsertion);
    }

    /**
     * Gets the list of entities that are scheduled for update
     *
     * @return Models\IEntity[] The list of entities scheduled for update
     */
    public function getScheduledEntityUpdates()
    {
        return array_values($this->scheduledForUpdate);
    }

    /**
     * Gets whether or not an entity is being managed
     *
     * @param Models\IEntity $entity The entity to check
     * @return bool True if the entity is managed, otherwise false
     */
    public function isManaged(Models\IEntity $entity)
    {
        return $this->getEntityState($entity) == EntityStates::MANAGED;
    }

    /**
     * Adds entities to manage
     *
     * @param Models\IEntity[] $entities The entities to manage
     */
    public function manageEntities(array $entities)
    {
        foreach($entities as $entity)
        {
            $this->manageEntity($entity);
        }
    }

    /**
     * Adds an entity to manage
     *
     * @param Models\IEntity $entity The entity to manage
     */
    public function manageEntity(Models\IEntity $entity)
    {
        $className = get_class($entity);
        $objectHashId = $this->getObjectHashId($entity);

        if(!isset($this->managedEntities[$className]))
        {
            $this->managedEntities[$className] = [];
        }

        // Don't double-manage an entity
        if(!isset($this->managedEntities[$className][$entity->getId()]))
        {
            $this->managedEntities[$className][$entity->getId()] = $entity;
            $this->entityStates[$this->getObjectHashId($entity)] = EntityStates::MANAGED;
            $this->objectHashIdsToOriginalData[$objectHashId] = clone $entity;
        }
    }

    /**
     * Registers a data mapper for a class
     *
     * @param string $className The name of the class whose data mapper we're registering
     * @param DataMappers\IDataMapper $dataMapper The data mapper for the class
     */
    public function registerDataMapper($className, DataMappers\IDataMapper $dataMapper)
    {
        $this->dataMappers[$className] = $dataMapper;
    }

    /**
     * Schedules an entity for deletion
     *
     * @param Models\IEntity $entity The entity to schedule for deletion
     */
    public function scheduleForDeletion(Models\IEntity $entity)
    {
        $this->scheduledForDeletion[$this->getObjectHashId($entity)] = $entity;
    }

    /**
     * Schedules an entity for insertion
     *
     * @param Models\IEntity $entity The entity to schedule for insertion
     */
    public function scheduleForInsertion(Models\IEntity $entity)
    {
        $this->scheduledForInsertion[$this->getObjectHashId($entity)] = $entity;
        $objectHashId = $this->getObjectHashId($entity);
        $this->entityStates[$objectHashId] = EntityStates::ADDED;
    }

    /**
     * Schedules an entity for insertion
     *
     * @param Models\IEntity $entity The entity to schedule for insertion
     */
    public function scheduleForUpdate(Models\IEntity $entity)
    {
        $this->scheduledForUpdate[$this->getObjectHashId($entity)] = $entity;
    }

    /**
     * Performs any actions after the commit
     */
    protected function postCommit()
    {
        /**
         * @var string $className
         * @var DataMappers\IDataMapper $dataMapper
         */
        foreach($this->dataMappers as $className => $dataMapper)
        {
            if($dataMapper instanceof DataMappers\ICachedSQLDataMapper)
            {
                // Now that the database writes have been committed, we can write to cache
                /** @var DataMappers\ICachedSQLDataMapper $dataMapper */
                $dataMapper->syncCache();
            }
        }
    }

    /**
     * Performs any actions after a rollback
     */
    protected function postRollback()
    {
        // Left blank simply to provide a hook for extending classes
    }

    /**
     * Checks for any changes made to entities, and if any are found, they're scheduled for update
     */
    private function checkForUpdates()
    {
        foreach($this->managedEntities as $className => $entities)
        {
            /** @var Models\IEntity $entity */
            foreach($entities as $entityId => $entity)
            {
                $objectHashId = $this->getObjectHashId($entity);

                // No point in checking for changes if it's already scheduled for an action
                if($this->isManaged($entity)
                    && !isset($this->scheduledForInsertion[$objectHashId])
                    && !isset($this->scheduledForUpdate[$objectHashId])
                    && !isset($this->scheduledForDeletion[$objectHashId])
                )
                {
                    // Get all the properties in the original entity and the current one
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

                    if(count(array_diff($currentPropertiesAsHash, $originalPropertiesAsHash)) > 0)
                    {
                        $this->scheduleForUpdate($entity);
                    }
                }
            }
        }
    }

    /**
     * Attempts to update all the entities scheduled for deletion
     */
    private function delete()
    {
        /** @var Models\IEntity $entity */
        foreach($this->scheduledForDeletion as $objectHashId => $entity)
        {
            $dataMapper = $this->getDataMapper(get_class($entity));
            $dataMapper->delete($entity);
            // Order here matters
            $this->detach($entity);
            $this->entityStates[$objectHashId] = EntityStates::DELETED;
        }
    }

    /**
     * Gets a unique hash Id for an object
     *
     * @param mixed $object The object whose hash we want
     * @return string The object hash Id
     */
    private function getObjectHashId($object)
    {
        return spl_object_hash($object);
    }

    /**
     * Attempts to insert all the entities scheduled for insertion
     */
    private function insert()
    {
        /** @var Models\IEntity $entity */
        foreach($this->scheduledForInsertion as $objectHashId => $entity)
        {
            $dataMapper = $this->getDataMapper(get_class($entity));
            $dataMapper->add($entity);
            $this->manageEntity($entity);
        }
    }

    /**
     * Attempts to update all the entities scheduled for updating
     */
    private function update()
    {
        /** @var Models\IEntity $entity */
        foreach($this->scheduledForUpdate as $objectHashId => $entity)
        {
            $dataMapper = $this->getDataMapper(get_class($entity));
            $dataMapper->update($entity);
            $this->manageEntity($entity);
        }
    }
} 