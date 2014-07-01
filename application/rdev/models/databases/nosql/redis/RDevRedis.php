<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines an extension of the PHPRedis library
 */
namespace RDev\Models\Databases\NoSQL\Redis;
use RDev\Models\Databases\SQL\Providers\TypeMapper;
use RDev\Models\Exceptions;

class RDevRedis extends \Redis
{
    /** @var Server The server we're connecting to */
    private $server = null;
    /** @var TypeMapper The type mapper to use for converting data to/from Redis */
    private $typeMapper = null;

    /**
     * @param Server $server The server we're connecting to
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
        $this->typeMapper = new TypeMapper();
        parent::connect($this->server->getHost(), $this->server->getPort());
    }

    /**
     * Closes the connection
     */
    public function __destruct()
    {
        parent::close();
    }

    /**
     * Deletes all the keys that match the input patterns
     * If you know the specific key(s) to delete, call RDevRedis' delete command instead because this method is computationally expensive
     *
     * @param array|string The key pattern or list of key patterns to delete
     * @return bool True if successful, otherwise false
     */
    public function deleteKeyPatterns($keyPatterns)
    {
        if(is_string($keyPatterns))
        {
            $keyPatterns = [$keyPatterns];
        }

        // Loops through our key patterns, gets all keys that match them, then deletes each of them
        $lua = "local keyPatterns = {'" . implode("','", $keyPatterns) . "'}
            for i, keyPattern in ipairs(keyPatterns) do
                for j, key in ipairs(redis.call('keys', keyPattern)) do
                    redis.call('del', key)
                end
            end";
        $this->eval($lua);

        return $this->getLastError() === null;
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @return TypeMapper
     */
    public function getTypeMapper()
    {
        return $this->typeMapper;
    }
}