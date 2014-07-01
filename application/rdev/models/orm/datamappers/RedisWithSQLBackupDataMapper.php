<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a data mapper that uses Redis as a cache with SQL as a backup
 */
namespace RDev\Models\ORM\DataMappers;
use RDev\Models;
use RDev\Models\Databases\NoSQL\Redis;
use RDev\Models\Databases\SQL;
use RDev\Models\Exceptions;
use RDev\Models\ORM\Exceptions as ORMExceptions;

abstract class RedisWithSQLBackupDataMapper implements ICachedDataMapper
{
    /** @var RedisDataMapper The Redis mapper to use for temporary storage */
    protected $redisDataMapper = null;
    /** @var SQLDataMapper The SQL database data mapper to use for permanent storage */
    protected $sqlDataMapper = null;
    /** @var Models\IEntity[] The list of entities scheduled for insertion */
    protected $scheduledForCacheInsertion = [];
    /** @var Models\IEntity[] The list of entities scheduled for update */
    protected $scheduledForCacheUpdate = [];
    /** @var Models\IEntity[] The list of entities scheduled for deletion */
    protected $scheduledForCacheDeletion = [];

    /**
     * @param Redis\RDevRedis $redis The RDevRedis object used in the Redis data mapper
     * @param SQL\ConnectionPool $connectionPool The connection pool used in the SQL data mapper
     */
    public function __construct(Redis\RDevRedis $redis, SQL\ConnectionPool $connectionPool)
    {
        $this->redisDataMapper = $this->getRedisDataMapper($redis);
        $this->sqlDataMapper = $this->getSQLDataMapper($connectionPool);
    }

    /**
     * {@inheritdoc}
     */
    public function add(Models\IEntity &$entity)
    {
        $this->sqlDataMapper->add($entity);
        $this->scheduleForCacheInsertion($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Models\IEntity &$entity)
    {
        $this->sqlDataMapper->delete($entity);
        $this->scheduleForCacheDeletion($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function syncCache()
    {
        try
        {
            // Insert entities
            foreach($this->scheduledForCacheInsertion as $entity)
            {
                $this->redisDataMapper->add($entity);
            }

            // Update entities
            foreach($this->scheduledForCacheUpdate as $entity)
            {
                $this->redisDataMapper->update($entity);
            }

            // Delete entities
            foreach($this->scheduledForCacheDeletion as $entity)
            {
                $this->redisDataMapper->delete($entity);
            }
        }
        catch(\Exception $ex)
        {
            Exceptions\Log::write("Failed to synchronize cache: " . $ex);
            throw new ORMExceptions\ORMException($ex->getMessage());
        }

        // Clear our schedules
        $this->scheduledForCacheInsertion = [];
        $this->scheduledForCacheUpdate = [];
        $this->scheduledForCacheDeletion = [];
    }

    /**
     * {@inheritdoc}
     */
    public function update(Models\IEntity &$entity)
    {
        $this->sqlDataMapper->update($entity);
        $this->scheduleForCacheUpdate($entity);
    }

    /**
     * Gets a Redis data mapper to use in this repo
     *
     * @param Redis\RDevRedis $redis The RDevRedis object used in the data mapper
     * @return RedisDataMapper The Redis data mapper to use
     */
    abstract protected function getRedisDataMapper(Redis\RDevRedis $redis);

    /**
     * Gets a SQL data mapper to use in this repo
     *
     * @param SQL\ConnectionPool $connectionPool The connection pool used in the data mapper
     * @return SQLDataMapper The SQL data mapper to use
     */
    abstract protected function getSQLDataMapper(SQL\ConnectionPool $connectionPool);

    /**
     * Attempts to retrieve an entity(ies) from the Redis data mapper before resorting to an SQL database
     *
     * @param string $funcName The name of the method we want to call on our data mappers
     * @param array $getFuncArgs The array of function arguments to pass in to our entity retrieval functions
     * @param bool $addDataToRedisOnMiss True if we want to add the entity from the database to cache in case of a cache miss
     * @param array $setFuncArgs The array of function arguments to pass into the set functions in the case of a Redis miss
     * @return Models\IEntity|array|bool The entity(ies) if it was found, otherwise false
     */
    protected function read($funcName, array $getFuncArgs = [], $addDataToRedisOnMiss = true, array $setFuncArgs = [])
    {
        // Always attempt to retrieve from Redis first
        $data = call_user_func_array([$this->redisDataMapper, $funcName], $getFuncArgs);

        // If we have to go off to SQL
        if($data === false)
        {
            $data = call_user_func_array([$this->sqlDataMapper, $funcName], $getFuncArgs);

            // Try to store the data back to Redis
            if($data === false)
            {
                return false;
            }

            if($addDataToRedisOnMiss)
            {
                if(!is_array($data))
                {
                    $data = [$data];
                }

                foreach($data as $datum)
                {
                    call_user_func_array([$this->redisDataMapper, "add"], array_merge([&$datum], $setFuncArgs));
                }
            }
        }

        return $data;
    }

    /**
     * Schedules an entity for deletion from cache
     *
     * @param Models\IEntity $entity The entity to schedule
     */
    protected function scheduleForCacheDeletion(Models\IEntity $entity)
    {
        $this->scheduledForCacheDeletion[] = $entity;
    }

    /**
     * Schedules an entity for insertion into cache
     *
     * @param Models\IEntity $entity The entity to schedule
     */
    protected function scheduleForCacheInsertion(Models\IEntity $entity)
    {
        $this->scheduledForCacheInsertion[] = $entity;
    }

    /**
     * Schedules an entity for update in cache
     *
     * @param Models\IEntity $entity The entity to schedule
     */
    protected function scheduleForCacheUpdate(Models\IEntity $entity)
    {
        $this->scheduledForCacheUpdate[] = $entity;
    }
} 