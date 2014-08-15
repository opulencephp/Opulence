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
     * @param Server $server The server we're connecting to
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
        $this->typeMapper = new TypeMapper();
        $connectionOptions = [
            "host" => $this->server->getHost(),
            "port" => $this->server->getPort(),
            "database" => $this->server->getDatabaseIndex()
        ];

        if($this->server->passwordIsSet())
        {
            $connectionOptions["password"] = $server->getPassword();
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