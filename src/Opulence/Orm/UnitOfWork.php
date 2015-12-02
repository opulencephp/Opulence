<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Orm;

use Exception;
use Opulence\Databases\IConnection;
use Opulence\Orm\ChangeTracking\IChangeTracker;
use Opulence\Orm\DataMappers\ICachedSqlDataMapper;
use Opulence\Orm\DataMappers\IDataMapper;
use Opulence\Orm\Ids\Accessors\IIdAccessorRegistry;
use Opulence\Orm\Ids\Generators\IIdGeneratorRegistry;
use Opulence\Orm\Ids\Generators\SequenceIdGenerator;
use RuntimeException;

/**
 * Defines a unit of work that tracks changes made to entities and atomically persists them
 */
class UnitOfWork implements IUnitOfWork
{
    /** @var IConnection The connection to use in our unit of work */
    private $connection = null;
    /** @var IEntityRegistry What manages/tracks entities for our unit of work */
    private $entityRegistry = null;
    /** @var IIdAccessorRegistry The Id accessor registry */
    private $idAccessorRegistry = null;
    /** @var IIdGeneratorRegistry The Id generator registry */
    private $idGeneratorRegistry = null;
    /** @var IChangeTracker The change tracker */
    private $changeTracker = null;
    /** @var array The mapping of class names to their data mappers */
    private $dataMappers = [];
    /** @var array The list of entities scheduled for insertion */
    private $scheduledForInsertion = [];
    /** @var array The list of entities scheduled for update */
    private $scheduledForUpdate = [];
    /** @var array The list of entities scheduled for deletion */
    private $scheduledForDeletion = [];

    /**
     * @param IEntityRegistry $entityRegistry The entity registry to use
     * @param IIdAccessorRegistry $idAccessorRegistry The Id accessor registry to use
     * @param IIdGeneratorRegistry $idGeneratorRegistry The Id generator registry to use
     * @param IChangeTracker $changeTracker The change tracker to use
     * @param IConnection $connection The connection to use in our unit of work
     */
    public function __construct(
        IEntityRegistry $entityRegistry,
        IIdAccessorRegistry $idAccessorRegistry,
        IIdGeneratorRegistry $idGeneratorRegistry,
        IChangeTracker $changeTracker,
        IConnection $connection = null
    ) {
        $this->entityRegistry = $entityRegistry;
        $this->idAccessorRegistry = $idAccessorRegistry;
        $this->idGeneratorRegistry = $idGeneratorRegistry;
        $this->changeTracker = $changeTracker;

        if ($connection !== null) {
            $this->setConnection($connection);
        }
    }

    /**
     * @inheritdoc
     */
    public function commit()
    {
        if (!$this->connection instanceof IConnection) {
            throw new OrmException("Connection not set");
        }

        $this->checkForUpdates();
        $this->preCommit();
        $this->connection->beginTransaction();

        try {
            $this->insert();
            $this->update();
            $this->delete();
            $this->connection->commit();
        } catch (Exception $ex) {
            $this->connection->rollBack();
            $this->postRollback();
            throw new OrmException("Commit failed", 0, $ex);
        }

        $this->postCommit();

        // Clear our schedules
        $this->scheduledForInsertion = [];
        $this->scheduledForUpdate = [];
        $this->scheduledForDeletion = [];
        $this->entityRegistry->clearAggregateRoots();
    }

    /**
     * @inheritdoc
     */
    public function detach($entity)
    {
        $this->entityRegistry->deregisterEntity($entity);
        $objectHashId = $this->entityRegistry->getObjectHashId($entity);
        unset($this->scheduledForInsertion[$objectHashId]);
        unset($this->scheduledForUpdate[$objectHashId]);
        unset($this->scheduledForDeletion[$objectHashId]);
    }

    /**
     * @inheritdoc
     */
    public function dispose()
    {
        $this->scheduledForInsertion = [];
        $this->scheduledForUpdate = [];
        $this->scheduledForDeletion = [];
        $this->entityRegistry->clearAggregateRoots();
        $this->entityRegistry->clear();
    }

    /**
     * @inheritdoc
     */
    public function getEntityRegistry()
    {
        return $this->entityRegistry;
    }

    /**
     * @inheritdoc
     */
    public function registerDataMapper($className, IDataMapper $dataMapper)
    {
        $this->dataMappers[$className] = $dataMapper;
    }

    /**
     * @inheritdoc
     */
    public function scheduleForDeletion($entity)
    {
        $this->scheduledForDeletion[$this->entityRegistry->getObjectHashId($entity)] = $entity;
    }

    /**
     * @inheritdoc
     */
    public function scheduleForInsertion($entity)
    {
        $objectHashId = $this->entityRegistry->getObjectHashId($entity);
        $this->scheduledForInsertion[$objectHashId] = $entity;
        $this->entityRegistry->setState($entity, EntityStates::QUEUED);
    }

    /**
     * @inheritdoc
     */
    public function scheduleForUpdate($entity)
    {
        $this->scheduledForUpdate[$this->entityRegistry->getObjectHashId($entity)] = $entity;
    }

    /**
     * @inheritdoc
     */
    public function setConnection(IConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Checks for any changes made to entities, and if any are found, they're scheduled for update
     */
    protected function checkForUpdates()
    {
        $managedEntities = $this->entityRegistry->getEntities();

        foreach ($managedEntities as $entity) {
            $objectHashId = $this->entityRegistry->getObjectHashId($entity);

            if ($this->entityRegistry->isRegistered($entity)
                && !isset($this->scheduledForInsertion[$objectHashId])
                && !isset($this->scheduledForUpdate[$objectHashId])
                && !isset($this->scheduledForDeletion[$objectHashId])
                && $this->changeTracker->hasChanged($entity)
            ) {
                $this->scheduleForUpdate($entity);
            }
        }
    }

    /**
     * Attempts to update all the entities scheduled for deletion
     */
    protected function delete()
    {
        foreach ($this->scheduledForDeletion as $objectHashId => $entity) {
            $dataMapper = $this->getDataMapper($this->entityRegistry->getClassName($entity));
            $dataMapper->delete($entity);
            // Order here matters
            $this->detach($entity);
            $this->entityRegistry->setState($entity, EntityStates::DEQUEUED);
        }
    }

    /**
     * Gets the data mapper for the input class
     *
     * @param string $className The name of the class whose data mapper we're searching for
     * @return IDataMapper The data mapper for the input class
     * @throws RuntimeException Thrown if there was no data mapper for the input class name
     */
    protected function getDataMapper($className)
    {
        if (!isset($this->dataMappers[$className])) {
            throw new RuntimeException("No data mapper for $className");
        }

        return $this->dataMappers[$className];
    }

    /**
     * Gets the list of entities that are scheduled for deletion
     *
     * @return object[] The list of entities scheduled for deletion
     */
    protected function getScheduledEntityDeletions()
    {
        return array_values($this->scheduledForDeletion);
    }

    /**
     * Gets the list of entities that are scheduled for insertion
     *
     * @return object[] The list of entities scheduled for insertion
     */
    protected function getScheduledEntityInsertions()
    {
        return array_values($this->scheduledForInsertion);
    }

    /**
     * Gets the list of entities that are scheduled for update
     *
     * @return object[] The list of entities scheduled for update
     */
    protected function getScheduledEntityUpdates()
    {
        return array_values($this->scheduledForUpdate);
    }

    /**
     * Attempts to insert all the entities scheduled for insertion
     */
    protected function insert()
    {
        foreach ($this->scheduledForInsertion as $objectHashId => $entity) {
            // If this entity was a child of aggregate roots, then call its methods to set the aggregate root Id
            $this->entityRegistry->runAggregateRootCallbacks($entity);
            $className = $this->entityRegistry->getClassName($entity);
            $dataMapper = $this->getDataMapper($className);
            $idGenerator = $this->idGeneratorRegistry->getIdGenerator($className);

            if ($idGenerator !== null) {
                if ($idGenerator instanceof SequenceIdGenerator) {
                    $idGenerator->setConnection($this->connection);
                }

                if ($idGenerator->isPostInsert()) {
                    $dataMapper->add($entity);
                    $this->idAccessorRegistry->setEntityId(
                        $entity,
                        $idGenerator->generate($entity)
                    );
                } else {
                    $this->idAccessorRegistry->setEntityId(
                        $entity,
                        $idGenerator->generate($entity)
                    );
                    $dataMapper->add($entity);
                }
            }

            $this->entityRegistry->registerEntity($entity);
        }
    }

    /**
     * Performs any actions after the commit
     */
    protected function postCommit()
    {
        /** @var IDataMapper $dataMapper */
        foreach ($this->dataMappers as $className => $dataMapper) {
            if ($dataMapper instanceof ICachedSqlDataMapper) {
                // Now that the database writes have been committed, we can write to cache
                /** @var ICachedSqlDataMapper $dataMapper */
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
        foreach ($this->scheduledForInsertion as $objectHashId => $entity) {
            $idGenerator = $this->idGeneratorRegistry->getIdGenerator($this->entityRegistry->getClassName($entity));

            if ($idGenerator !== null) {
                $this->idAccessorRegistry->setEntityId(
                    $entity,
                    $idGenerator->getEmptyValue($entity)
                );
            }
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
     * Attempts to update all the entities scheduled for updating
     */
    protected function update()
    {
        foreach ($this->scheduledForUpdate as $objectHashId => $entity) {
            // If this entity was a child of aggregate roots, then call its methods to set the aggregate root Id
            $this->entityRegistry->runAggregateRootCallbacks($entity);
            $dataMapper = $this->getDataMapper($this->entityRegistry->getClassName($entity));
            $dataMapper->update($entity);
            $this->entityRegistry->registerEntity($entity);
        }
    }
} 