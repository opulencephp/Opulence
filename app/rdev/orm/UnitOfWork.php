<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a unit of work that tracks changes made to entities and atomically persists them
 */
namespace RDev\ORM;
use RDev\Databases\SQL;
use RDev\Exceptions;

class UnitOfWork
{
    /** @var SQL\IConnection The connection to use in our unit of work */
    private $connection = null;
    /** @var IEntityManager What manages/tracks entities for our unit of work */
    private $entityManager = null;
    /** @var array The mapping of class names to their data mappers */
    private $dataMappers = [];
    /** @var array The list of entities scheduled for insertion */
    private $scheduledForInsertion = [];
    /** @var array The list of entities scheduled for update */
    private $scheduledForUpdate = [];
    /** @var array The list of entities scheduled for deletion */
    private $scheduledForDeletion = [];
    /**
     * Maps aggregate root children to their roots as well as functions that can set the child's aggregate root Id
     * Each entry is an array of arrays with the following keys:
     *      "aggregateRoot" => The aggregate root
     *      "child" => The entity whose aggregate root Id will be set to the Id of the aggregate root
     *      "function" => The function to execute that actually sets the aggregate root Id in the child
     *          Note:  The function MUST have two parameters: first for the aggregate root and a second for the child
     *
     * @var array
     */
    private $aggregateRootChildren = [];

    /**
     * @param SQL\IConnection $connection The connection to use in our unit of work
     * @param IEntityManager $entityManager The entity manager to use
     */
    public function __construct(SQL\IConnection $connection, IEntityManager $entityManager)
    {
        $this->connection = $connection;
        $this->entityManager = $entityManager;
    }

    /**
     * Commits any entities that have been scheduled for insertion/updating/deletion
     *
     * @throws ORMException Thrown if there was an error committing the transaction
     */
    public function commit()
    {
        $this->checkForUpdates();
        $this->preCommit();
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
            $this->connection->rollBack();
            $this->postRollback();
            throw new ORMException($ex->getMessage());
        }

        $this->postCommit();

        // Clear our schedules
        $this->scheduledForInsertion = [];
        $this->scheduledForUpdate = [];
        $this->scheduledForDeletion = [];
        $this->aggregateRootChildren = [];
    }

    /**
     * Detaches an entity from being managed
     *
     * @param IEntity $entity The entity to detach
     */
    public function detach(IEntity $entity)
    {
        $this->entityManager->detach($entity);
        $objectHashId = $this->entityManager->getObjectHashId($entity);
        unset($this->scheduledForInsertion[$objectHashId]);
        unset($this->scheduledForUpdate[$objectHashId]);
        unset($this->scheduledForDeletion[$objectHashId]);
        unset($this->aggregateRootChildren[$objectHashId]);
    }

    /**
     * Disposes of all data in this unit of work
     */
    public function dispose()
    {
        $this->scheduledForInsertion = [];
        $this->scheduledForUpdate = [];
        $this->scheduledForDeletion = [];
        $this->aggregateRootChildren = [];
        $this->entityManager->dispose();
    }

    /**
     * Gets the data mapper for the input class
     *
     * @param string $className The name of the class whose data mapper we're searching for
     * @return DataMappers\SQLDataMapper The data mapper for the input class
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
     * @return IEntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Gets the list of entities that are scheduled for deletion
     *
     * @return IEntity[] The list of entities scheduled for deletion
     */
    public function getScheduledEntityDeletions()
    {
        return array_values($this->scheduledForDeletion);
    }

    /**
     * Gets the list of entities that are scheduled for insertion
     *
     * @return IEntity[] The list of entities scheduled for insertion
     */
    public function getScheduledEntityInsertions()
    {
        return array_values($this->scheduledForInsertion);
    }

    /**
     * Gets the list of entities that are scheduled for update
     *
     * @return IEntity[] The list of entities scheduled for update
     */
    public function getScheduledEntityUpdates()
    {
        return array_values($this->scheduledForUpdate);
    }

    /**
     * Registers a function to set the aggregate root Id in a child entity after the aggregate root has been inserted
     * Since the child depends on the aggregate root's Id being set, make sure the root is inserted before the child
     *
     * @param IEntity $aggregateRoot The aggregate root
     * @param IEntity $child The child of the aggregate root
     * @param callable $function The function that contains the logic to set the aggregate root Id in the child
     */
    public function registerAggregateRootChild(IEntity $aggregateRoot, IEntity $child, callable $function)
    {
        $childObjectHashId = $this->entityManager->getObjectHashId($child);

        if(!isset($this->aggregateRootChildren[$childObjectHashId]))
        {
            $this->aggregateRootChildren[$childObjectHashId] = [];
        }

        $this->aggregateRootChildren[$childObjectHashId][] = [
            "aggregateRoot" => $aggregateRoot,
            "child" => $child,
            "function" => $function
        ];
    }

    /**
     * Registers a data mapper for a class
     * Registering a data mapper for a class will overwrite any previously-set data mapper for that class
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
     * @param IEntity $entity The entity to schedule for deletion
     */
    public function scheduleForDeletion(IEntity $entity)
    {
        $this->scheduledForDeletion[$this->entityManager->getObjectHashId($entity)] = $entity;
    }

    /**
     * Schedules an entity for insertion
     *
     * @param IEntity $entity The entity to schedule for insertion
     */
    public function scheduleForInsertion(IEntity $entity)
    {
        $objectHashId = $this->entityManager->getObjectHashId($entity);
        $this->scheduledForInsertion[$objectHashId] = $entity;
        $this->entityManager->setState($entity, EntityStates::ADDED);
    }

    /**
     * Schedules an entity for insertion
     *
     * @param IEntity $entity The entity to schedule for insertion
     */
    public function scheduleForUpdate(IEntity $entity)
    {
        $this->scheduledForUpdate[$this->entityManager->getObjectHashId($entity)] = $entity;
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
                $dataMapper->commit();
            }
        }
    }

    /**
     * Performs any actions after a rollback
     */
    protected function postRollback()
    {
        // Unset each of the new entities' Ids
        /** @var IEntity $entity */
        foreach($this->scheduledForInsertion as $objectHashId => $entity)
        {
            $dataMapper = $this->getDataMapper($this->entityManager->getClassName($entity));
            $entity->setId($dataMapper->getIdGenerator()->getEmptyValue());
        }
    }

    /**
     * Performs any actions before a commit
     */
    protected function preCommit()
    {
        // Leave blank for extending classes to implement
    }

    /**
     * Checks for any changes made to entities, and if any are found, they're scheduled for update
     */
    private function checkForUpdates()
    {
        $managedEntities = $this->entityManager->getManagedEntities();

        foreach($managedEntities as $entity)
        {
            $objectHashId = $this->entityManager->getObjectHashId($entity);

            if($this->entityManager->isManaged($entity)
                && !isset($this->scheduledForInsertion[$objectHashId])
                && !isset($this->scheduledForUpdate[$objectHashId])
                && !isset($this->scheduledForDeletion[$objectHashId])
                && $this->entityManager->hasChanged($entity)
            )
            {
                $this->scheduleForUpdate($entity);
            }
        }
    }

    /**
     * Attempts to update all the entities scheduled for deletion
     */
    private function delete()
    {
        /** @var IEntity $entity */
        foreach($this->scheduledForDeletion as $objectHashId => $entity)
        {
            $dataMapper = $this->getDataMapper($this->entityManager->getClassName($entity));
            $dataMapper->delete($entity);
            // Order here matters
            $this->detach($entity);
            $this->entityManager->setState($entity, EntityStates::DELETED);
        }
    }

    /**
     * Executes the aggregate root functions, if there any for the input entity
     *
     * @param string $objectHashId The object hash Id of the child
     * @param IEntity $child The child entity
     */
    private function doAggregateRootFunctions($objectHashId, IEntity $child)
    {
        if(isset($this->aggregateRootChildren[$objectHashId]))
        {
            foreach($this->aggregateRootChildren[$objectHashId] as $aggregateRootData)
            {
                $aggregateRoot = $aggregateRootData["aggregateRoot"];
                $aggregateRootData["function"]($aggregateRoot, $child);
            }
        }
    }

    /**
     * Attempts to insert all the entities scheduled for insertion
     */
    private function insert()
    {
        /** @var IEntity $entity */
        foreach($this->scheduledForInsertion as $objectHashId => $entity)
        {
            // If this entity was a child of aggregate roots, then call its methods to set the aggregate root Id
            $this->doAggregateRootFunctions($objectHashId, $entity);
            $dataMapper = $this->getDataMapper($this->entityManager->getClassName($entity));
            $dataMapper->add($entity);
            $entity->setId($dataMapper->getIdGenerator()->generate($entity, $this->connection));
            $this->entityManager->manage($entity);
        }
    }

    /**
     * Attempts to update all the entities scheduled for updating
     */
    private function update()
    {
        /** @var IEntity $entity */
        foreach($this->scheduledForUpdate as $objectHashId => $entity)
        {
            // If this entity was a child of aggregate roots, then call its methods to set the aggregate root Id
            $this->doAggregateRootFunctions($objectHashId, $entity);
            $dataMapper = $this->getDataMapper($this->entityManager->getClassName($entity));
            $dataMapper->update($entity);
            $this->entityManager->manage($entity);
        }
    }
} 