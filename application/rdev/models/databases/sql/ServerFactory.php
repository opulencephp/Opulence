<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines methods for creating server objects
 */
namespace RDev\Models\Databases\SQL;

class ServerFactory
{
    /**
     * Creates a server object from an array config
     *
     * @param array|Server $config The config to create the server from or the already-instantiated server object
     *      The reason we allow already-instantiated server objects is in case a user has created classes for his/her
     *      various servers
     *      If an array, it must contain the following keys mapped to their appropriate values:
     *          "host" => server host,
     *          "username" => server username credential,
     *          "password" => server password credential,
     *          "databaseName" => name of database on server to use,
     *          "system" => system used by the server
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

        if(!$this->validateConfig($config))
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

    /**
     * Gets whether or not a server config is valid
     *
     * @param array|Server $config The config or server to validate
     * @return bool True if the server config is valid, otherwise false
     */
    private function validateConfig($config)
    {
        if($config instanceof Server)
        {
            return true;
        }

        // We assume at this point that this is an array configuration
        if(!is_array($config))
        {
            return false;
        }

        $requiredFields = ["host", "username", "password", "databaseName"];

        foreach($requiredFields as $requiredField)
        {
            if(!isset($config[$requiredField]))
            {
                return false;
            }
        }

        if(isset($config["port"]) && !is_numeric($config["port"]))
        {
            return false;
        }

        if(isset($config["charset"]) && !is_string($config["charset"]))
        {
            return false;
        }

        return true;
    }
} 