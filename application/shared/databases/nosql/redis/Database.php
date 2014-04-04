<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a Redis database
 */
namespace RamODev\Application\Shared\Databases\NoSQL\Redis;
use RamODev\Application\Shared\Exceptions;
use RamODev\Application\Shared\Databases;
use RamODev\Application\Shared\Databases\NoSQL\Exceptions as NoSQLExceptions;

class Database extends Databases\Database
{
    /** @var Server The server we're connecting to */
    protected $server = null;
    /** @var \Redis The Redis object we use to cache items */
    private $redis = null;

    /**
     * @param Server $server The server we're connecting to
     */
    public function __construct(Server $server)
    {
        parent::__construct($server);

        $this->redis = new \Redis();
    }

    /**
     * Closes the connection
     */
    public function close()
    {
        if($this->isConnected())
        {
            $this->redis->close();
        }
    }

    /**
     * Attempts to connect to the server
     *
     * @return bool True if we connected successfully, otherwise false
     */
    public function connect()
    {
        $this->redis->connect($this->server->getHost(), $this->server->getPort());

        if(!$this->isConnected())
        {
            Exceptions\Log::write("Unable to connect to cache on host " . $this->server->getHost());
        }

        return $this->isConnected();
    }

    /**
     * Deletes all the keys that match the input patterns
     * If you know the specific key(s) to delete, call Redis' delete command instead because this method is computationally expensive
     *
     * @param array|string The key pattern or list of key patterns to delete
     * @return bool True if successful, otherwise false
     */
    public function deleteKeyPatterns($keyPatterns)
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
        $this->redis->eval($lua);

        return $this->redis->getLastError() === null;
    }

    /**
     * @return \Redis
     */
    public function getPHPRedis()
    {
        return $this->redis;
    }

    /**
     * @return \RamODev\Application\Databases\NoSQL\Redis\Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Gets whether or not we're connected
     *
     * @return bool True if we're connected, otherwise false
     */
    public function isConnected()
    {
        return $this->redis->isConnected();
    }
}