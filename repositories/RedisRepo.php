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
     * The list of key patterns this repository uses
     * It is recommended that you add all key patterns to this list to make it easier to eventually flush them, if we want to
     *
     * @var array
     */
    protected $keyPatterns = array();

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
     * Adds a key pattern(s) to the list
     *
     * @param string|array $keyPatterns The key pattern or list of key patterns to add
     */
    protected function addKeyPattern($keyPatterns)
    {
        // The key patterns must be an array, so create one if necessary
        if(is_string($keyPatterns))
        {
            $keyPatterns = array($keyPatterns);
        }

        foreach($keyPatterns as $keyPattern)
        {
            if(!in_array($keyPattern, $this->keyPatterns))
            {
                $this->keyPatterns[] = $keyPattern;
            }
        }
    }

    /**
     * Deletes all the key patterns
     *
     * @return bool True if successful, otherwise false
     */
    protected function deleteKeyPatterns()
    {
        // Loops through our key patterns, gets all keys that match them, then deletes each of them
        $lua = "local keyPatterns = {'" . implode("','", $this->keyPatterns) . "'}
            for i, keyPattern in ipairs(keyPatterns) do
                for j, key in ipairs(redis.call('keys', keyPattern)) do
                    redis.call('del', key)
                end
            end";
        $this->redisDatabase->getPHPRedis()->eval($lua);

        return $this->redisDatabase->getPHPRedis()->getLastError() === null;
    }
} 