<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the server config
 */
namespace RDev\Databases\NoSQL\Redis\Configs;
use RDev\Configs;
use RDev\Databases\NoSQL\Redis;

class ServerConfig extends Configs\Config
{
    /**
     * {@inheritdoc}
     */
    public function exchangeArray($configArray)
    {
        if(!$this->isValid($configArray))
        {
            throw new \RuntimeException("Invalid Redis config");
        }

        if(!$configArray["servers"]["master"] instanceof Redis\Server)
        {
            $masterConfigArray = $configArray["servers"]["master"];
            $master = new Redis\Server();
            $master->setHost($masterConfigArray["host"]);
            $master->setPort($masterConfigArray["port"]);

            if(isset($masterConfigArray["password"]))
            {
                $master->setPassword($masterConfigArray["password"]);
            }

            if(isset($masterConfigArray["databaseIndex"]))
            {
                $master->setDatabaseIndex($masterConfigArray["databaseIndex"]);
            }

            if(isset($masterConfigArray["connectionTimeout"]))
            {
                $master->setConnectionTimeout($masterConfigArray["connectionTimeout"]);
            }

            $configArray["servers"]["master"] = $master;
        }

        $this->configArray = $configArray;
    }

    /**
     * {@inheritdoc}
     */
    protected function isValid(array $configArray)
    {
        if(!$this->hasRequiredFields($configArray, [
            "servers" => [
                "master" => null
            ]
        ])
        )
        {
            return false;
        }

        // Only accept server objects or valid server config arrays
        if(is_array($configArray["servers"]["master"]))
        {
            if(!$this->serverIsValid($configArray["servers"]["master"]))
            {
                return false;
            }
        }
        elseif(!$configArray["servers"]["master"] instanceof Redis\Server)
        {
            return false;
        }

        return true;
    }

    /**
     * Validates a server config array
     *
     * @param array $configArray The array of config options
     *      It must contain the following keys mapped to their appropriate values:
     *          "host" => server host,
     *          "port" => server port,
     *      The following keys are optional:
     *          "password" => server password,
     *          "databaseIndex" => index of the database to use on this server,
     *          "connectionTimeout" => the number of seconds to wait before a timeout
     * @return bool True if the config is valid, otherwise false
     */
    private function serverIsValid(array $configArray)
    {
        if(!$this->hasRequiredFields($configArray, [
            "host" => null,
            "port" => null
        ])
        )
        {
            return false;
        }

        if(!is_string($configArray["host"]))
        {
            return false;
        }

        if(!is_numeric($configArray["port"]))
        {
            return false;
        }

        if(isset($configArray["password"]) && !is_string($configArray["password"]))
        {
            return false;
        }

        if(isset($configArray["databaseIndex"]) && !is_numeric($configArray["databaseIndex"]))
        {
            return false;
        }

        if(isset($configArray["connectionTimeout"]) && !is_numeric($configArray["connectionTimeout"]))
        {
            return false;
        }

        return true;
    }
} 