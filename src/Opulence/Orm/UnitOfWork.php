<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

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
    /**
     * The list of scheduled actions to take on entities
     * The keys are the object hash Ids, which map to a subarray whose first element is the type of action
     * (eg "delete", "insert", or "update") and second element is the entity to take action on
     * Note that the values of this array will be null if the entity is detached
     *
     * @var array
     */
    protected array $scheduledActions = [];
    /** @var int The count of all scheduled actions (even unset ones), which is used for performance */
    private int $scheduledActionCount = 0;
    /** @var IConnection The connection to use in our unit of work */
    private IConnection $connection;
    /** @var IEntityRegistry What manages/tracks entities for our unit of work */
    private IEntityRegistry $entityRegistry;
    /** @var IIdAccessorRegistry The Id accessor registry */
    private IIdAccessorRegistry $idAccessorRegistry;
    /** @var IIdGeneratorRegistry The Id generator registry */
    private IIdGeneratorRegistry $idGeneratorRegistry;
    /** @var IChangeTracker The change tracker */
    private IChangeTracker $changeTracker;
    /** @var array The mapping of class names to their data mappers */
    private array $dataMappers = [];
    /**
     * The mapping of object hash Ids to the index of the action array element that holds the entity
     * We use this to index entities that are scheduled for insertion, which makes detaching them faster
     *
     * @var array
     */
    private $scheduledForInsertion = [];
    /**
     * The mapping of object hash Ids to the index of the action array element that holds the entity
     * We use this to index entities that are scheduled for update, which makes detaching them faster
     *
     * @var array
     */
    private $scheduledForUpdate = [];
    /**
     * The mapping of object hash Ids to the index of the action array element that holds the entity
     * We use this to index entities that are scheduled for deletion, which makes detaching them faster
     *
     * @var array
     */
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
        IConnection $connection
    ) {
        $this->entityRegistry = $entityRegistry;
        $this->idAccessorRegistry = $idAccessorRegistry;
        $this->idGeneratorRegistry = $idGeneratorRegistry;
        $this->changeTracker = $changeTracker;
        $this->connection = $connection;
    }

    /**
     * @inheritdoc
     */
    public function commit(): void
    {
        if (!$this->connection instanceof IConnection) {
            throw new OrmException('Connection not set');
        }

        $this->checkForUpdates();
        $this->preCommit();
        $this->connection->beginTransaction();

        try {
            foreach ($this->scheduledActions as $action) {
                if ($action[1] === null) {
                    continue;
                }

                switch ($action[0]) {
                    case 'insert':
                        $this->insert($action[1]);
                        break;
                    case 'update':
                        $this->update($action[1]);
                        break;
                    case 'delete':
                        $this->delete($action[1]);
                        break;
                    default:
                        throw new RuntimeException("Invalid action type {$action[0]}");
                }
            }

            $this->connection->commit();
        } catch (Exception $ex) {
            $this->connection->rollBack();
            $this->postRollback();
            throw new OrmException('Commit failed', 0, $ex);
        }

        $this->postCommit();

        // Clear our schedules
        $this->scheduledActions = [];
        $this->scheduledActionCount = 0;
        $this->scheduledForInsertion = [];
        $this->scheduledForUpdate = [];
        $this->scheduledForDeletion = [];
        $this->entityRegistry->clearAggregateRoots();
    }

    /**
     * @inheritdoc
     */
    public function detach(object $entity): void
    {
        $this->entityRegistry->deregisterEntity($entity);
        $objectHashId = $this->entityRegistry->getObjectHashId($entity);
        // Remove all scheduled actions and the entity from our action indices
        unset(
            $this->scheduledActions[$this->scheduledForInsertion[$objectHashId] ?? null],
            $this->scheduledActions[$this->scheduledForUpdate[$objectHashId] ?? null],
            $this->scheduledActions[$this->scheduledForDeletion[$objectHashId] ?? null],
            $this->scheduledForInsertion[$objectHashId],
            $this->scheduledForUpdate[$objectHashId],
            $this->scheduledForDeletion[$objectHashId]
        );
    }

    /**
     * @inheritdoc
     */
    public function dispose(): void
    {
        $this->scheduledActions = [];
        $this->scheduledActionCount = 0;
        $this->scheduledForInsertion = [];
        $this->scheduledForUpdate = [];
        $this->scheduledForDeletion = [];
        $this->entityRegistry->clearAggregateRoots();
        $this->entityRegistry->clear();
    }

    /**
     * @inheritdoc
     */
    public function getEntityRegistry(): IEntityRegistry
    {
        return $this->entityRegistry;
    }

    /**
     * @inheritdoc
     */
    public function registerDataMapper(string $className, IDataMapper $dataMapper): void
    {
        $this->dataMappers[$className] = $dataMapper;
    }

    /**
     * @inheritdoc
     */
    public function scheduleForDeletion(object $entity): void
    {
        $objectHashId = $this->entityRegistry->getObjectHashId($entity);
        $this->scheduledActions[] = ['delete', $entity];
        $this->scheduledActionCount++;
        $this->scheduledForDeletion[$objectHashId] = $this->scheduledActionCount - 1;
    }

    /**
     * @inheritdoc
     */
    public function scheduleForInsertion(object $entity): void
    {
        $objectHashId = $this->entityRegistry->getObjectHashId($entity);
        $this->scheduledActions[] = ['insert', $entity];
        $this->scheduledActionCount++;
        $this->scheduledForInsertion[$objectHashId] = $this->scheduledActionCount - 1;
        $this->entityRegistry->setState($entity, EntityStates::QUEUED);
    }

    /**
     * @inheritdoc
     */
    public function scheduleForUpdate(object $entity): void
    {
        $objectHashId = $this->entityRegistry->getObjectHashId($entity);
        $this->scheduledActions[] = ['update', $entity];
        $this->scheduledActionCount++;
        $this->scheduledForUpdate[$objectHashId] = $this->scheduledActionCount - 1;
    }

    /**
     * Checks for any changes made to entities, and if any are found, they're scheduled for update
     */
    protected function checkForUpdates(): void
    {
        $managedEntities = $this->entityRegistry->getEntities();

        foreach ($managedEntities as $entity) {
            $objectHashId = $this->entityRegistry->getObjectHashId($entity);

            if (
                !isset($this->scheduledForInsertion[$objectHashId])
                && !isset($this->scheduledForUpdate[$objectHashId])
                && !isset($this->scheduledForDeletion[$objectHashId])
                && $this->entityRegistry->isRegistered($entity)
                && $this->changeTracker->hasChanged($entity)
            ) {
                $this->scheduleForUpdate($entity);
            }
        }
    }

    /**
     * Attempts to update all the entities scheduled for deletion
     *
     * @param object $entity The entity to delete
     * @throws OrmException Thrown if there was an error deleting the entity
     */
    protected function delete(object $entity): void
    {
        $dataMapper = $this->getDataMapper($this->entityRegistry->getClassName($entity));
        $dataMapper->delete($entity);
        // Order here matters
        $this->detach($entity);
        $this->entityRegistry->setState($entity, EntityStates::DEQUEUED);
    }

    /**
     * Gets the data mapper for the input class
     *
     * @param string $className The name of the class whose data mapper we're searching for
     * @return IDataMapper The data mapper for the input class
     * @throws RuntimeException Thrown if there was no data mapper for the input class name
     */
    protected function getDataMapper(string $className): IDataMapper
    {
        if (!isset($this->dataMappers[$className])) {
            throw new RuntimeException("No data mapper for $className");
        }

        return $this->dataMappers[$className];
    }

    /**
     * Attempts to insert all the entities scheduled for insertion
     *
     * @param object $entity The entity to insert
     * @throws OrmException Thrown if there was an error inserting the entity
     */
    protected function insert(object $entity): void
    {
        // If this entity was a child of aggregate roots, then call its methods to set the aggregate root Id
        $this->entityRegistry->runAggregateRootCallbacks($entity);
        $className = $this->entityRegistry->getClassName($entity);
        $dataMapper = $this->getDataMapper($className);
        $idGenerator = $this->idGeneratorRegistry->getIdGenerator($className);

        if ($idGenerator === null) {
            $dataMapper->add($entity);
        } else {
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

    /**
     * Performs any actions after the commit
     */
    protected function postCommit(): void
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
    protected function postRollback(): void
    {
        // Unset the inserted entities' Ids
        foreach ($this->scheduledForInsertion as $objectHashId => $index) {
            if (($entity = $this->scheduledActions[$index][1] ?? null) === null) {
                continue;
            }

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
    protected function preCommit(): void
    {
        // Leave blank for extending classes to implement
    }

    /**
     * Attempts to update all the entities scheduled for updating
     *
     * @param object $entity The entity to update
     * @throws OrmException Thrown if there was an error updating the entity
     */
    protected function update(object $entity): void
    {
        // If this entity was a child of aggregate roots, then call its methods to set the aggregate root Id
        $this->entityRegistry->runAggregateRootCallbacks($entity);
        $dataMapper = $this->getDataMapper($this->entityRegistry->getClassName($entity));
        $dataMapper->update($entity);
        $this->entityRegistry->registerEntity($entity);
    }
}
