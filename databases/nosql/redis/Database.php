<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a Redis database
 */
namespace RamODev\Databases\NoSQL\Redis;
use RamODev\Exceptions;
use RamODev\Databases;
use RamODev\Databases\NoSQL\Exceptions as CacheExceptions;

require_once(__DIR__ . "/../../Database.php");
require_once(__DIR__ . "/../exceptions/NoSQLException.php");

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
     * @return \Redis
     */
    public function getRedis()
    {
        return $this->redis;
    }

    /**
     * @return \RamODev\Databases\NoSQL\Redis\Server
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