<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Orm\ChangeTracking;

use Opulence\Orm\OrmException;
use ReflectionClass;

/**
 * Defines the change tracker
 */
class ChangeTracker implements IChangeTracker
{
    /** @var object[] The mapping of object Ids to their original data */
    protected $objectHashIdsToOriginalData = [];
    /**
     * The mapping of class names to comparison functions
     * Each function should return true if the entities are the same, otherwise false
     *
     * @var callable[]
     */
    protected $comparators = [];

    /**
     * @inheritdoc
     */
    public function hasChanged($entity) : bool
    {
        if (!isset($this->objectHashIdsToOriginalData[spl_object_hash($entity)])) {
            throw new OrmException('Entity is not registered');
        }

        // If a comparison function was specified, we don't bother using reflection to check for updates
        if (isset($this->comparators[get_class($entity)])) {
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
    public function registerComparator(string $className, callable $comparator)
    {
        $this->comparators[$className] = $comparator;
    }

    /**
     * @inheritdoc
     */
    public function startTracking($entity)
    {
        $objectHashId = spl_object_hash($entity);
        $this->objectHashIdsToOriginalData[$objectHashId] = clone $entity;
    }

    /**
     * @inheritdoc
     */
    public function stopTracking($entity)
    {
        unset($this->objectHashIdsToOriginalData[spl_object_hash($entity)]);
    }

    /**
     * @inheritdoc
     */
    public function stopTrackingAll()
    {
        $this->objectHashIdsToOriginalData = [];
    }

    /**
     * Checks to see if an entity has changed using a comparison function
     *
     * @param object $entity The entity to check for changes
     * @return bool True if the entity has changed, otherwise false
     */
    protected function hasChangedUsingComparisonFunction($entity) : bool
    {
        $objectHashId = spl_object_hash($entity);
        $originalData = $this->objectHashIdsToOriginalData[$objectHashId];

        return !$this->comparators[get_class($entity)]($originalData, $entity);
    }

    /**
     * Checks to see if an entity has changed using reflection
     *
     * @param object $entity The entity to check for changes
     * @return bool True if the entity has changed, otherwise false
     */
    protected function hasChangedUsingReflection($entity) : bool
    {
        // Get all the properties in the original entity and the current one
        $objectHashId = spl_object_hash($entity);
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

        if (count($originalProperties) !== count($currentProperties)) {
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
