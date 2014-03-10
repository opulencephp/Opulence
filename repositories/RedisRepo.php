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
     * Performs the read query for object(s) and returns any results
     * This assumes that the keys for all the objects are stored in a set
     *
     * @param string $keyOfObjectKeyIndex The key of the index that contains all the objects we're searching for
     * @param string $objectFromHashFuncName The name of the (protected or public) method to run, which creates an object from a Redis hash
     * @param bool $expectSingleResult True if we're expecting a single result, otherwise false and we're expecting an array of results
     * @return array|mixed|bool The list of objects or an individual object if successful, otherwise false
     */
    protected function read($keyOfObjectKeyIndex, $objectFromHashFuncName, $expectSingleResult)
    {
        if($expectSingleResult)
        {
            $objectKeys = $this->redisDatabase->getPHPRedis()->get($keyOfObjectKeyIndex);

            if($objectKeys === false)
            {
                return false;
            }

            // To be compatible with the rest of this method, we'll convert the key to an array containing that key
            $objectKeys = array($objectKeys);
        }
        else
        {
            $objectKeys = $this->redisDatabase->getPHPRedis()->sMembers($keyOfObjectKeyIndex);

            if(count($objectKeys) == 0)
            {
                return false;
            }
        }

        $objects = array();

        // Create and store the object associated with each key
        foreach($objectKeys as $objectKey)
        {
            $object = call_user_func_array(array($this, $objectFromHashFuncName), array($objectKey));

            if($object === array())
            {
                return false;
            }

            $objects[] = $object;
        }

        if($expectSingleResult)
        {
            return $objects[0];
        }
        else
        {
            return $objects;
        }
    }
} 