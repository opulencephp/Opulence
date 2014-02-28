<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a repository that uses a Redis database as a storage method
 */
namespace RamODev\Repositories;
use RamODev\Databases\NoSQL\Redis;

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
     * Deletes all the keys that match the input patterns
     * If you know the specific key to delete, call deleteKeys() instead because this method is computationally expensive
     *
     * @param array|string The key pattern or list of key patterns to delete
     * @return bool True if successful, otherwise false
     */
    protected function deleteKeyPatterns($keyPatterns)
    {
        if(is_string($keyPatterns))
        {
            $keyPatterns = array($keyPatterns);
        }

        // Loops through our key patterns, gets all keys that match them, then deletes each of them
        $lua = "local keyPatterns = {'" . implode("','", $keyPatterns) . "'}
            for i, keyPattern in ipairs(keyPatterns) do
                for j, key in ipairs(redis.call('keys', keyPattern)) do
                    redis.call('del', key)
                end
            end";
        $this->redisDatabase->getPHPRedis()->eval($lua);

        return $this->redisDatabase->getPHPRedis()->getLastError() === null;
    }

    /**
     * Deletes all the keys
     * This differs from deleteKeyPatterns() because this matches specific keys, not patterns
     *
     * @param array|string The keys or list of keys to delete
     * @return bool True if successful, otherwise false
     */
    protected function deleteKeys($keys)
    {
        if(is_string($keys))
        {
            $keys = array($keys);
        }

        $lua = "local keys = {'" . implode("','", $keys) . "'}
            for j, key in ipairs(keys) do
                redis.call('del', key)
            end";
        $this->redisDatabase->getPHPRedis()->eval($lua);

        return $this->redisDatabase->getPHPRedis()->getLastError() === null;
    }
} 