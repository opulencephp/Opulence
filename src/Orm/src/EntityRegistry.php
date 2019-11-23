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

use Opulence\Orm\ChangeTracking\ChangeTracker;
use Opulence\Orm\ChangeTracking\IChangeTracker;
use Opulence\Orm\Ids\Accessors\IdAccessorRegistry;
use Opulence\Orm\Ids\Accessors\IIdAccessorRegistry;

/**
 * Defines an entity registry
 */
final class EntityRegistry implements IEntityRegistry
{
    /** @var IIdAccessorRegistry The Id accessory registry */
    protected IIdAccessorRegistry $idAccessorRegistry;
    /** @var IChangeTracker The change tracker */
    protected IChangeTracker $changeTracker;
    /** @var array The mapping of entities' object hash Ids to their various states */
    private array $entityStates = [];
    /** @var array The mapping of class names to a list of entities of that class */
    private array $entities = [];
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
    private array $aggregateRootChildren = [];

    /**
     * @param IIdAccessorRegistry|null $idAccessorRegistry The Id accessor registry
     * @param IChangeTracker|null $changeTracker The change tracker
     */
    public function __construct(IIdAccessorRegistry $idAccessorRegistry = null, IChangeTracker $changeTracker = null)
    {
        $this->idAccessorRegistry = $idAccessorRegistry ?? new IdAccessorRegistry();
        $this->changeTracker = $changeTracker ?? new ChangeTracker();
    }

    /**
     * @inheritdoc
     */
    public function clear(): void
    {
        $this->changeTracker->stopTrackingAll();
        $this->entities = [];
        $this->entityStates = [];
        $this->clearAggregateRoots();
    }

    /**
     * @inheritdoc
     */
    public function clearAggregateRoots(): void
    {
        $this->aggregateRootChildren = [];
    }

    /**
     * @inheritdoc
     */
    public function deregisterEntity(object $entity): void
    {
        $entityState = $this->getEntityState($entity);
        unset($this->aggregateRootChildren[$this->getObjectHashId($entity)]);

        if ($entityState === EntityStates::QUEUED || $entityState === EntityStates::REGISTERED) {
            $className = $this->getClassName($entity);
            $objectHashId = $this->getObjectHashId($entity);
            $entityId = (string) $this->idAccessorRegistry->getEntityId($entity);
            $this->entityStates[$objectHashId] = EntityStates::UNREGISTERED;
            unset($this->entities[$className][$entityId]);
            $this->changeTracker->stopTracking($entity);
        }
    }

    /**
     * @inheritdoc
     */
    public function getClassName(object $object): string
    {
        return get_class($object);
    }

    /**
     * @inheritdoc
     */
    public function getEntities(): array
    {
        if (count($this->entities) === 0) {
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
    public function getEntity(string $className, $id): ?object
    {
        $index = (string) $id;
        if (!isset($this->entities[$className][$index])) {
            return null;
        }

        return $this->entities[$className][$index];
    }

    /**
     * @inheritdoc
     */
    public function getEntityState(object $entity): int
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
    public function getObjectHashId(object $object): string
    {
        return spl_object_hash($object);
    }

    /**
     * @inheritdoc
     */
    public function isRegistered(object $entity): bool
    {
        try {
            $entityId = (string) $this->idAccessorRegistry->getEntityId($entity);

            return $this->getEntityState($entity) === EntityStates::REGISTERED
            || isset($this->entities[$this->getClassName($entity)][$entityId]);
        } catch (OrmException $ex) {
            return false;
        }
    }

    /**
     * Registers a function to set the aggregate root Id in a child entity after the aggregate root has been inserted
     * Since the child depends on the aggregate root's Id being set, make sure the root is inserted before the child
     *
     * @param object $aggregateRoot The aggregate root
     * @param object $child The child of the aggregate root
     * @param callable $function The function that contains the logic to set the aggregate root Id in the child
     */
    public function registerAggregateRootCallback(object $aggregateRoot, object $child, callable $function): void
    {
        $childObjectHashId = $this->getObjectHashId($child);

        if (!isset($this->aggregateRootChildren[$childObjectHashId])) {
            $this->aggregateRootChildren[$childObjectHashId] = [];
        }

        $this->aggregateRootChildren[$childObjectHashId][] = [
            'aggregateRoot' => $aggregateRoot,
            'child' => $child,
            'function' => $function
        ];
    }

    /**
     * @inheritdoc
     */
    public function registerEntity(object &$entity): void
    {
        $className = $this->getClassName($entity);
        $entityId = (string) $this->idAccessorRegistry->getEntityId($entity);

        if (!isset($this->entities[$className])) {
            $this->entities[$className] = [];
        }

        if (isset($this->entities[$className][$entityId])) {
            // Change the reference of the input entity to the one that's already registered
            $entity = $this->getEntity($className, $entityId);
        } else {
            // Register this entity
            $this->changeTracker->startTracking($entity);
            $this->entities[$className][$entityId] = $entity;
            $this->entityStates[$this->getObjectHashId($entity)] = EntityStates::REGISTERED;
        }
    }

    /**
     * @inheritdoc
     */
    public function runAggregateRootCallbacks(object $child): void
    {
        $objectHashId = $this->getObjectHashId($child);

        if (isset($this->aggregateRootChildren[$objectHashId])) {
            foreach ($this->aggregateRootChildren[$objectHashId] as $aggregateRootData) {
                $aggregateRoot = $aggregateRootData['aggregateRoot'];
                $aggregateRootData['function']($aggregateRoot, $child);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function setState($entity, int $entityState): void
    {
        $this->entityStates[$this->getObjectHashId($entity)] = $entityState;
    }
}
