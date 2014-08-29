<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines methods for creating server objects
 */
namespace RDev\Models\Databases\SQL;
use RDev\Models\Databases\SQL\Configs;

class ServerFactory
{
    /**
     * Creates a server object from a config
     *
     * @param Configs\ServerConfig|array|Server $config The config to create the server from or the already-instantiated server object
     *      The reason we allow already-instantiated server objects is in case a user has created classes for his/her
     *      various servers
     *      If an array or server config, it must contain the following keys mapped to their appropriate values:
     *          "host" => server host,
     *          "username" => server username credential,
     *          "password" => server password credential,
     *          "databaseName" => name of database on server to use
     *      The following keys are optional:
     *          "port" => server port,
     *          "charset" => character set
     * @return Server The server object created from the config
     * @throws \RuntimeException Thrown if the config isn't valid
     */
    public function createFromConfig($config)
    {
        if($config instanceof Server)
        {
            return $config;
        }

        if(is_array($config))
        {
            $config = new Configs\ServerConfig($config);
        }

        if(!$config->isValid())
        {
            throw new \RuntimeException("Invalid server configuration");
        }

        $server = new Server();
        $server->setHost($config["host"]);
        $server->setUsername($config["username"]);
        $server->setPassword($config["password"]);
        $server->setDatabaseName($config["databaseName"]);

        if(isset($config["port"]))
        {
            $server->setPort($config["port"]);
        }

        if(isset($config["charset"]))
        {
            $server->setCharset($config["charset"]);
        }

        return $server;
    }
} 