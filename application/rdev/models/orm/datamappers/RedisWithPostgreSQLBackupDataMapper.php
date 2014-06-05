<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a data mapper that uses Redis as a cache with PostgreSQL as a backup
 */
namespace RDev\Models\ORM\DataMappers;
use RDev\Models;
use RDev\Models\Databases\NoSQL\Redis;
use RDev\Models\Databases\SQL;

abstract class RedisWithPostgreSQLBackupDataMapper implements IDataMapper
{
    /** @var RedisDataMapper The Redis mapper to use for temporary storage */
    protected $redisDataMapper = null;
    /** @var PostgreSQLDataMapper The SQL database data mapper to use for permanent storage */
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
     * Gets a PostgreSQL data mapper to use in this repo
     *
     * @param SQL\RDevPDO $pdo The RDevPDO object used in the data mapper
     * @return PostgreSQLDataMapper The PostgreSQL data mapper to use
     */
    abstract protected function getPostgreSQLDataMapper(SQL\RDevPDO $pdo);

    /**
     * Gets a Redis data mapper to use in this repo
     *
     * @param Redis\RDevRedis $redis The RDevRedis object used in the data mapper
     * @return RedisDataMapper The Redis data mapper to use
     */
    abstract protected function getRedisDataMapper(Redis\RDevRedis $redis);

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
} 