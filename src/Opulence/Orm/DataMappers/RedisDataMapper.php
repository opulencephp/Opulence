<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Orm\DataMappers;

use Opulence\Redis\Redis;

/**
 * Defines a data mapper that maps domain data to and from Redis
 */
abstract class RedisDataMapper implements ICacheDataMapper
{
    /** Defines a string value */
    const VALUE_TYPE_STRING = 0;
    /** Defines a set value */
    const VALUE_TYPE_SET = 1;
    /** Defines a sorted set value */
    const VALUE_TYPE_SORTED_SET = 2;

    /** @var Redis The Redis cache to use for queries */
    protected $redis = null;

    /**
     * @param Redis $redis The Redis cache to use for queries
     */
    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        $entityHash = $this->getEntityHashById($id);

        if ($entityHash === null || $entityHash === []) {
            return null;
        }

        return $this->loadEntity($entityHash);
    }

    /**
     * Gets the hash representation of an entity
     *
     * @param int|string $id The Id of the entity whose hash we're searching for
     * @return array|null The entity's hash if successful, otherwise null
     */
    abstract protected function getEntityHashById($id);

    /**
     * Gets the list of members of the set at the given key
     * We need this to know how to get the set members from the concrete Redis cache object this mapper uses
     *
     * @param string $key The key whose members we want
     * @return array|bool The list of members if successful, otherwise false
     */
    abstract protected function getSetMembersFromRedis(string $key);

    /**
     * Gets the list of members of the sorted set at the given key
     * We need this to know how to get the sorted set members from the concrete Redis cache object this mapper uses
     *
     * @param string $key The key whose members we want
     * @return array|bool The list of members if successful, otherwise false
     */
    abstract protected function getSortedSetMembersFromRedis(string $key);

    /**
     * Gets the item at the given key
     * We need this to know how to get the value from the concrete Redis cache object this mapper uses
     *
     * @param string $key The key whose value we want
     * @return mixed|bool The value of the key
     */
    abstract protected function getValueFromRedis(string $key);

    /**
     * Loads an entity from a hash of data
     *
     * @param array $hash The hash of data to load the entity from
     * @return object The entity
     */
    abstract protected function loadEntity(array $hash);

    /**
     * Loads multiple entities from their Ids
     *
     * @param array $entityIds The list of Ids of entities to load
     * @return array|null The list of entities if they were all found in cache, otherwise null
     */
    protected function loadEntities(array $entityIds)
    {
        if (count($entityIds) === 0) {
            return null;
        }

        $entities = [];

        // Create and store the entities associated with each Id
        foreach ($entityIds as $entityId) {
            $hash = $this->getEntityHashById($entityId);

            if ($hash === null) {
                return null;
            }

            $entities[] = $this->loadEntity($hash);
        }

        return $entities;
    }

    /**
     * Performs the read query for entity(ies) and returns any results
     * This assumes that the Ids for all the entities are stored in a set, sorted set, or a string
     *
     * @param string $keyOfEntityIds The key that contains the Id(s) of the entities we're searching for
     * @param int $valueType The constant indicating the type of value at the key
     * @return array|mixed|null The list of entities or an individual entity if successful, otherwise null
     */
    protected function read(string $keyOfEntityIds, int $valueType)
    {
        switch ($valueType) {
            case self::VALUE_TYPE_STRING:
                $entityIds = $this->getValueFromRedis($keyOfEntityIds);

                if ($entityIds === false) {
                    return null;
                }

                // To be compatible with the rest of this method, we'll convert the Id to an array containing that Id
                $entityIds = [$entityIds];

                break;
            case self::VALUE_TYPE_SET:
                $entityIds = $this->getSetMembersFromRedis($keyOfEntityIds);

                if (count($entityIds) === 0) {
                    return null;
                }

                break;
            case self::VALUE_TYPE_SORTED_SET:
                $entityIds = $this->getSortedSetMembersFromRedis($keyOfEntityIds);

                if (count($entityIds) === 0) {
                    return null;
                }

                break;
            default:
                return null;
        }

        $entities = $this->loadEntities($entityIds);

        if ($valueType === self::VALUE_TYPE_STRING) {
            return $entities[0];
        }

        return $entities;
    }
}
