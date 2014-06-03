<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a data mapper that maps domain data to and from Redis
 */
namespace RDev\Models\Repositories\DataMappers;
use RDev\Models;
use RDev\Models\Databases\NoSQL\Redis;
use RDev\Models\Repositories\Exceptions;

abstract class RedisDataMapper
{
    /** @var Redis\RDevRedis The RDevRedis object to use for queries */
    protected $rDevRedis = null;

    /**
     * @param Redis\RDevRedis $rDevRedis The RDevRedis object to use for queries
     */
    public function __construct(Redis\RDevRedis $rDevRedis)
    {
        $this->rDevRedis = $rDevRedis;
    }

    /**
     * Adds an entity to the database
     *
     * @param Models\IEntity $entity The entity to add
     * @throws Exceptions\RepoException Thrown if the entity couldn't be added
     */
    abstract public function add(Models\IEntity &$entity);

    /**
     * Flushes items from Redis that are in this mapper
     *
     * @throws Exceptions\RepoException Thrown if Redis couldn't be flushed
     */
    abstract public function flush();

    /**
     * Saves any changes made to an entity
     *
     * @param Models\IEntity $entity The entity to save
     * @throws Exceptions\RepoException Thrown if the entity couldn't be saved
     */
    abstract public function save(Models\IEntity &$entity);

    /**
     * Gets the entity by Id
     *
     * @param int $id The Id of the entity we're searching for
     * @return Models\IEntity|bool The entity with the Id if successful, otherwise false
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
            $entityIds = $this->rDevRedis->get($keyOfEntityIds);

            if($entityIds === false)
            {
                return false;
            }

            // To be compatible with the rest of this method, we'll convert the Id to an array containing that Id
            $entityIds = [$entityIds];
        }
        else
        {
            $entityIds = $this->rDevRedis->sMembers($keyOfEntityIds);

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