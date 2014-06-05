<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the skeleton for repository classes that use Redis with a PostgreSQL backup
 */
namespace RDev\Models\ORM\Repositories;
use RDev\Models\Databases\NoSQL\Redis;
use RDev\Models\Databases\SQL;
use RDev\Models\ORM\DataMappers;

abstract class RedisWithPostgreSQLBackupRepo implements IRedisWithSQLBackupRepo
{
    /** @var DataMappers\RedisDataMapper The Redis mapper to use for temporary storage */
    protected $redisDataMapper = null;
    /** @var DataMappers\PostgreSQLDataMapper The SQL database data mapper to use for permanent storage */
    protected $postgreSQLDataMapper = null;

    /**
     * @param Redis\RDevRedis $rDevRedis The RDevRedis object used in the Redis data mapper
     * @param SQL\RDevPDO $rDevPDO The RDevPDO object used in the PostgreSQL data mapper
     */
    public function __construct(Redis\RDevRedis $rDevRedis, SQL\RDevPDO $rDevPDO)
    {
        $this->redisDataMapper = $this->getRedisDataMapper($rDevRedis);
        $this->postgreSQLDataMapper = $this->getPostgreSQLDataMapper($rDevPDO);
    }

    /**
     * Synchronizes the Redis database with the SQL database
     *
     * @throws Exceptions\RepoException Thrown if there was an error syncing the data mappers
     */
    abstract public function sync();

    /**
     * Gets a PostgreSQL data mapper to use in this repo
     *
     * @param SQL\RDevPDO $rDevPDO The RDevPDO object used in the data mapper
     * @return DataMappers\PostgreSQLDataMapper The PostgreSQL data mapper to use
     */
    abstract protected function getPostgreSQLDataMapper(SQL\RDevPDO $rDevPDO);

    /**
     * Gets a Redis data mapper to use in this repo
     *
     * @param Redis\RDevRedis $rDevRedis The RDevRedis object used in the data mapper
     * @return DataMappers\RedisDataMapper The Redis data mapper to use
     */
    abstract protected function getRedisDataMapper(Redis\RDevRedis $rDevRedis);

    /**
     * Attempts to retrieve data from the Redis data mapper before resorting to an SQL database
     *
     * @param string $funcName The name of the method we want to call on our data mappers
     * @param array $getFuncArgs The array of function arguments to pass in to our data retrieval functions
     * @param bool $addDataToRedisOnMiss True if we want to add data from the database to cache in case of a cache miss
     * @param array $setFuncArgs The array of function arguments to pass into the data set functions in the case of a Redis miss
     * @return mixed|bool The data from the repository if it was found, otherwise false
     */
    protected function read($funcName, array $getFuncArgs = [], $addDataToRedisOnMiss = true, array $setFuncArgs = [])
    {
        // Always attempt to retrieve from Redis first
        $data = call_user_func_array([$this->redisDataMapper, $funcName], $getFuncArgs);

        // If we have to go off to PostgreSQL
        if($data === false)
        {
            $data = call_user_func_array([$this->postgreSQLDataMapper, $funcName], $getFuncArgs);

            // Try to store the data back to Redis
            if($data === false)
            {
                return false;
            }

            if($addDataToRedisOnMiss)
            {
                if(is_array($data))
                {
                    foreach($data as $datum)
                    {
                        call_user_func_array([$this->redisDataMapper, "add"], array_merge([&$datum], $setFuncArgs));
                    }
                }
                else
                {
                    call_user_func_array([$this->redisDataMapper, "add"], array_merge([&$data], $setFuncArgs));
                }
            }
        }

        return $data;
    }

    /**
     * Attempts to store/change data in the repo
     * This method should be called by subclasses to perform CREATE/UPDATE/DELETE-type actions
     *
     * @param string $funcName The name of the method we want to call on our data mappers
     * @param array $funcArgs The array of function arguments to pass in
     * @return bool True if successful, otherwise false
     */
    protected function write($funcName, array $funcArgs)
    {
        // We update PostgreSQL first in the case that it sets an SQL row Id to the object
        return call_user_func_array([$this->postgreSQLDataMapper, $funcName], $funcArgs)
        && call_user_func_array([$this->redisDataMapper, $funcName], $funcArgs);
    }
} 