<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the skeleton for repository classes to extend
 */
namespace RamODev\Repositories;
use RamODev\Databases\NoSQL\Redis;
use RamODev\Databases\SQL;

require_once(__DIR__ . "/ActionTypes.php");

abstract class Repo
{
    /** @var RedisRepo The Redis repository to use for temporary storage */
    protected $redisRepo = null;
    /** @var SQLRepo The SQL database repository to use for permanent storage */
    protected $sqlRepo = null;

    /**
     * @param Redis\Database $redisDatabase The Redis database used in the repo
     * @param SQL\Database $sqlDatabase The relational database used in the repo
     */
    public function __construct(Redis\Database $redisDatabase, SQL\Database $sqlDatabase)
    {
        $this->redisRepo = $this->getRedisRepo($redisDatabase);
        $this->sqlRepo = $this->getSQLRepo($sqlDatabase);
    }

    /**
     * In the case we're getting data and didn't find it in the Redis repo, we need a way to store it there for future use
     * The contents of this method should call the appropriate method to store data in the Redis repo
     *
     * @param mixed $data The data to write to the Redis repository
     */
    abstract protected function addDataToRedisRepo(&$data);

    /**
     * Gets a Redis repo to use in this repo
     *
     * @param Redis\Database $redisDatabase The Redis database used in the repo
     * @return RedisRepo The Redis repo to use
     */
    abstract protected function getRedisRepo(Redis\Database $redisDatabase);

    /**
     * Gets a SQL repo to use in this repo
     *
     * @param SQL\Database $sqlDatabase The SQL database used in the repo
     * @return SQLRepo The SQL repo to use
     */
    abstract protected function getSQLRepo(SQL\Database $sqlDatabase);

    /**
     * Attempts to retrieve data from the Redis repo before resorting to a SQL database
     *
     * @param string $funcName The name of the method we want to call on our sub-repo classes
     * @param array $funcArgs The array of function arguments to pass in
     * @return mixed|bool The data from the repository if it was found, otherwise false
     */
    protected function get($funcName, $funcArgs = array())
    {
        // Always attempt to retrieve from the Redis repo first
        $data = call_user_func_array(array($this->redisRepo, $funcName), $funcArgs);

        // If we have to go off to the SQL repo
        if($data === false)
        {
            $data = call_user_func_array(array($this->sqlRepo, $funcName), $funcArgs);

            // Try to store the data back to the Redis repo
            if($data === false)
            {
                return false;
            }
            elseif(is_array($data))
            {
                foreach($data as $datum)
                {
                    $this->addDataToRedisRepo($datum);
                }
            }
            else
            {
                $this->addDataToRedisRepo($data);
            }
        }

        return $data;
    }

    /**
     * Attempts to store/change data in the repos
     * This method should be called by subclasses to perform CREATE/UPDATE/DELETE-type actions
     *
     * @param string $funcName The name of the method we want to call on our sub-repo classes
     * @param array $funcArgs The array of function arguments to pass in
     * @return bool True if successful, otherwise false
     */
    protected function set($funcName, $funcArgs)
    {
        // We update the SQL repo first in the case that it sets an SQL row ID to the object
        return call_user_func_array(array($this->sqlRepo, $funcName), $funcArgs) && call_user_func_array(array($this->redisRepo, $funcName), $funcArgs);
    }
} 