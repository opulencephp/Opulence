<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Orm;

use Opulence\Orm\ChangeTracking\IChangeTracker;
use Opulence\Orm\Ids\IIdAccessorRegistry;

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
            $entityId = $this->idAccessorRegistry->getEntityId($entity);
            $this->entityStates[$objectHashId] = EntityStates::UNREGISTERED;
            unset($this->entities[$className][$entityId]);
            $this->changeTracker->stopTracking($entity);
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
    public function isRegistered($entity)
    {
        try {
            $entityId = $this->idAccessorRegistry->getEntityId($entity);

            return $this->getEntityState($entity) == EntityStates::REGISTERED
            || isset($this->entities[$this->getClassName($entity)][$entityId]);
        } catch (OrmException $ex) {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function registerEntity(&$entity)
    {
        $className = $this->getClassName($entity);
        $objectHashId = $this->getObjectHashId($entity);
        $entityId = $this->idAccessorRegistry->getEntityId($entity);

        if (!isset($this->entities[$className])) {
            $this->entities[$className] = [];
        }

        if (isset($this->entities[$className][$entityId])) {
            // Change the reference of the input entity to the one that's already registered
            $entity = $this->getEntity($this->getClassName($entity), $entityId);
        } else {
            // Register this entity
            $this->changeTracker->startTracking($entity);
            $this->entities[$className][$entityId] = $entity;
            $this->entityStates[$objectHashId] = EntityStates::REGISTERED;
        }
    }

    /**
     * @inheritdoc
     */
    public function setState($entity, $entityState)
    {
        $this->entityStates[$this->getObjectHashId($entity)] = $entityState;
    }
}