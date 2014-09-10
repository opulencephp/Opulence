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
     * @param Configs\ServerConfig|array $config The configuration to use for the server to connect to
     *      This must contain the following keys:
     *          "servers" => [
     *              "master" => [
     *                  "host" => server host,
     *                  "port" => server port
     *              ]
     *          ]
     *      The following keys are optional for the server:
     *          "password" => server password for authentication,
     *          "databaseIndex" => index of database to connect to on the server,
     *          "connectionTimeout" => the number of seconds to wait before a connection is timed out
     */
    public function __construct($config)
    {
        if(is_array($config))
        {
            $config = new Configs\ServerConfig($config);
        }

        $this->server = $config["servers"]["master"];
        $this->typeMapper = new TypeMapper();
        $connectionOptions = [
            "host" => $this->server->getHost(),
            "port" => $this->server->getPort(),
            "database" => $this->server->getDatabaseIndex()
        ];

        if($this->server->passwordIsSet())
        {
            $connectionOptions["password"] = $config->getPassword();
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