<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines an extension of the PHPRedis library
 */
namespace RDev\Models\Databases\NoSQL\Redis;
use RDev\Models\Exceptions;

class RDevPHPRedis extends \Redis implements IRedis
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

        parent::connect($this->server->getHost(), $this->server->getPort(), $this->server->getConnectionTimeout());
        parent::select($this->server->getDatabaseIndex());

        if($this->server->passwordIsSet())
        {
            parent::auth($this->server->getPassword());
        }
    }

    /**
     * Closes the connection
     */
    public function __destruct()
    {
        parent::close();
    }
}