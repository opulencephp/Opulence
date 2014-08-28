<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines an extension of the Predis library
 */
namespace RDev\Models\Databases\NoSQL\Redis;

// Register the autoloader for Predis
require("Predis/Autoloader.php");
Predis\Autoloader::register();

class RDevPredis extends \Predis\Client implements IRedis
{
    use TRedis;

    /**
     * @param array|string|Server $config The configuration to use for the server to connect to
     *      If it's a string, then it must point to a valid JSON file containing the config
     *          This JSON file should be decodable into the same format defined below for a keyed array
     *      If it's an array, then it must have the following format
     *      This must contain the following keys:
     *          "host" => server host,
     *          "port" => server port,
     *      The following keys are optional:
     *          "password" => server password for authentication,
     *          "databaseIndex" => index of database to connect to on the server,
     *          "connectionTimeout" => the number of seconds to wait before a connection is timed out
     */
    public function __construct($config)
    {
        $this->serverFactory = new ServerFactory();
        $this->server = $config;
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