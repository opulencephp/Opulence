<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines an extension of the Predis library
 */
namespace RDev\Models\Databases\NoSQL\Redis;
use RDev\Models\Databases\NoSQL\Redis\Configs;

class RDevPredis extends \Predis\Client implements IRedis
{
    use TRedis;

    /**
     * @param Server $server The server to use
     * @param TypeMapper $typeMapper The type mapper to use
     */
    public function __construct(Server $server, TypeMapper $typeMapper)
    {
        $this->server = $server;
        $this->typeMapper = $typeMapper;
        $connectionOptions = [
            "host" => $this->server->getHost(),
            "port" => $this->server->getPort(),
            "database" => $this->server->getDatabaseIndex()
        ];

        if($this->server->passwordIsSet())
        {
            $connectionOptions["password"] = $this->server->getPassword();
        }

        if($this->server->getConnectionTimeout() > 0)
        {
            $connectionOptions["connection_timeout"] = $this->server->getConnectionTimeout();
        }

        parent::__construct($connectionOptions);
    }

    /**
     * Closes the connection
     */
    public function __destruct()
    {
        parent::quit();
    }
} 