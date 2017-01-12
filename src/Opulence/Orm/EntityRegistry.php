<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Orm;

use Opulence\Orm\ChangeTracking\IChangeTracker;
use Opulence\Orm\Ids\Accessors\IIdAccessorRegistry;

/**
 * Defines an entity registry
 */
class EntityRegistry implements IEntityRegistry
{
    /** @var IIdAccessorRegistry The Id accessory registry */
    protected $idAccessorRegistry = null;
    /** @var IChangeTracker The change tracker */
    protected $changeTracker = null;
    /** @var array The mapping of entities' object hash Ids to their various states */
    private $entityStates = [];
    /** @var array The mapping of class names to a list of entities of that class */
    private $entities = [];
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
     * @param IIdAccessorRegistry $idAccessorRegistry The Id accessor registry
     * @param IChangeTracker $changeTracker The change tracker
     */
    public function __construct(IIdAccessorRegistry $idAccessorRegistry, IChangeTracker $changeTracker)
    {
        $this->idAccessorRegistry = $idAccessorRegistry;
        $this->changeTracker = $changeTracker;
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $this->changeTracker->stopTrackingAll();
        $this->entities = [];
        $this->entityStates = [];
        $this->clearAggregateRoots();
    }

    /**
     * @inheritdoc
     */
    public function clearAggregateRoots()
    {
        $this->aggregateRootChildren = [];
    }

    /**
     * @inheritdoc
     */
    public function deregisterEntity($entity)
    {
        $entityState = $this->getEntityState($entity);
        unset($this->aggregateRootChildren[$this->getObjectHashId($entity)]);

        if ($entityState === EntityStates::QUEUED || $entityState === EntityStates::REGISTERED) {
            $className = $this->getClassName($entity);
            $objectHashId = $this->getObjectHashId($entity);
            $entityId = $this->idAccessorRegistry->getEntityId($entity);
            $this->entityStates[$objectHashId] = EntityStates::UNREGISTERED;
            unset($this->entities[$className][$entityId]);
            $this->changeTracker->stopTracking($entity);
        }
    }

    /**
     * @inheritdoc
     */
    public function getClassName($object) : string
    {
        return get_class($object);
    }

    /**
     * @inheritdoc
     */
    public function getEntities() : array
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
    public function getEntity(string $className, $id)
    {
        if (!isset($this->entities[$className][$id])) {
            return null;
        }

        return $this->entities[$className][$id];
    }

    /**
     * @inheritdoc
     */
    public function getEntityState($entity) : int
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
    public function getObjectHashId($object) : string
    {
        return spl_object_hash($object);
    }

    /**
     * @inheritdoc
     */
    public function isRegistered($entity) : bool
    {
        try {
            $entityId = $this->idAccessorRegistry->getEntityId($entity);

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
    public function registerAggregateRootCallback($aggregateRoot, $child, callable $function)
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
    public function registerEntity(&$entity)
    {
        $className = $this->getClassName($entity);
        $entityId = $this->idAccessorRegistry->getEntityId($entity);

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
    public function runAggregateRootCallbacks($child)
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
    public function setState($entity, int $entityState)
    {
        $this->entityStates[$this->getObjectHashId($entity)] = $entityState;
    }
}
