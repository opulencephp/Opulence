<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a data mapper that maps domain data to and from Redis
 */
namespace RDev\Models\ORM\DataMappers;
use RDev\Models;
use RDev\Models\Databases\NoSQL\Redis;
use RDev\Models\ORM\Repositories\Exceptions;

abstract class RedisDataMapper implements IDataMapper
{
    /** @var Redis\RDevRedis The RDevRedis object to use for queries */
    protected $redis = null;

    /**
     * @param Redis\RDevRedis $redis The RDevRedis object to use for queries
     */
    public function __construct(Redis\RDevRedis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * Adds an entity to the database
     *
     * @param Models\IEntity $entity The entity to add
     * @throws Exceptions\RepoException Thrown if the entity couldn't be added
     */
    abstract public function add(Models\IEntity &$entity);

    /**
     * {@inheritdoc}
     */
    abstract public function delete(Models\IEntity &$entity);

    /**
     * Flushes items from Redis that are in this mapper
     *
     * @throws Exceptions\RepoException Thrown if Redis couldn't be flushed
     */
    abstract public function flush();

    /**
     * {@inheritdoc}
     */
    abstract public function getAll();

    /**
     * {@inheritdoc}
     */
    abstract public function loadEntity(array $hash);

    /**
     * {@inheritdoc}
     */
    abstract public function update(Models\IEntity &$entity);

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        $entityHash = $this->getEntityHashById($id);

        if($entityHash === false || $entityHash === [])
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
            $entityIds = $this->redis->get($keyOfEntityIds);

            if($entityIds === false)
            {
                return false;
            }

            // To be compatible with the rest of this method, we'll convert the Id to an array containing that Id
            $entityIds = [$entityIds];
        }
        else
        {
            $entityIds = $this->redis->sMembers($keyOfEntityIds);

            if(count($entityIds) == 0)
            {
                return false;
            }
        }

        $entities = [];

        // Create and store the entities associated with each Id
        foreach($entityIds as $entityId)
        {
            $hash = $this->getEntityHashById($entityId);

            if($hash === false)
            {
                return false;
            }

            $entities[] = $this->loadEntity($hash);
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