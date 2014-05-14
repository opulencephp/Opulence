<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a repository that uses a Redis database as a storage method
 */
namespace RamODev\Application\Shared\Models\Repositories;
use RamODev\Application\Shared\Models;
use RamODev\Application\Shared\Models\Databases\NoSQL\Redis;

abstract class RedisRepo
{
    /** @var Redis\Database The Redis database to use for queries */
    protected $redisDatabase = null;

    /**
     * @param Redis\Database $redisDatabase The database to use for queries
     */
    public function __construct(Redis\Database $redisDatabase)
    {
        $this->redisDatabase = $redisDatabase;
    }

    /**
     * Flushes items in this repo
     *
     * @return bool True if successful, otherwise false
     */
    abstract public function flush();

    /**
     * Gets the entity by Id
     *
     * @param int $id The Id of the entity we're searching for
     * @return Models\IEntity|bool The entity with the Id if successful, otherwise false
     */
    public function getById($id)
    {
        $entityHash = $this->getEntityHashById($id);

        if($entityHash === false || $entityHash === array())
        {
            return false;
        }

        return $this->loadEntity($entityHash);
    }

    /**
     * Gets the hash representation of an entity
     *
     * @param int $id The Id of the entity whose hash we're searching for
     * @return array|bool The entity's hash if successful, otherwise false
     */
    abstract protected function getEntityHashById($id);

    /**
     * Loads an entity from a hash of data
     *
     * @param array $hash The hash of data
     * @return Models\IEntity The entity
     */
    abstract protected function loadEntity(array $hash);

    /**
     * Performs the read query for entity(ies) and returns any results
     * This assumes that the Ids for all the entities are stored in a set
     *
     * @param string $keyOfEntityIds The key that contains the Id(s) of the entities we're searching for
     * @param bool $expectSingleResult True if we're expecting a single result, otherwise false
     * @return array|mixed|bool The list of entities or an individual entity if successful, otherwise false
     */
    protected function read($keyOfEntityIds, $expectSingleResult)
    {
        if($expectSingleResult)
        {
            $entityIds = $this->redisDatabase->getPHPRedis()->get($keyOfEntityIds);

            if($entityIds === false)
            {
                return false;
            }

            // To be compatible with the rest of this method, we'll convert the Id to an array containing that Id
            $entityIds = array($entityIds);
        }
        else
        {
            $entityIds = $this->redisDatabase->getPHPRedis()->sMembers($keyOfEntityIds);

            if(count($entityIds) == 0)
            {
                return false;
            }
        }

        $entities = array();

        // Create and store the entities associated with each Id
        foreach($entityIds as $entityId)
        {
            $hash = $this->getEntityHashById($entityId);

            if($hash === false)
            {
                return false;
            }

            $entity = $this->loadEntity($hash);

            $entities[] = $entity;
        }

        if($expectSingleResult)
        {
            return $entities[0];
        }
        else
        {
            return $entities;
        }
    }
} 