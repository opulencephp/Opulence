<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the connection pool config
 */
namespace RDev\Models\Databases\SQL\Configs;
use RDev\Models\Configs;
use RDev\Models\Databases\SQL;

class ConnectionPoolConfig extends Configs\Config
{
    /**
     * {@inheritdoc}
     */
    public function fromArray(array $configArray)
    {
        if(!$this->isValid($configArray))
        {
            throw new \RuntimeException("Invalid connection pool config");
        }

        $driver = $configArray["driver"];

        if(!$driver instanceof SQL\IDriver)
        {
            if(isset(SQL\ConnectionPool::$drivers[$driver]))
            {
                $configArray["driver"] = new SQL\ConnectionPool::$drivers[$driver]();
            }
            else
            {
                // We assume this is a custom driver class
                if(!class_exists($driver))
                {
                    throw new \RuntimeException("Invalid custom driver: " . $driver);
                }

                $configArray["driver"] = new $driver();

                if(!$configArray["driver"] instanceof SQL\IDriver)
                {
                    throw new \RuntimeException("Driver does not implement IDriver");
                }
            }
        }

        if(!$configArray["servers"]["master"] instanceof SQL\Server)
        {
            if(!is_array($configArray["servers"]["master"]))
            {
                throw new \RuntimeException("Invalid server config");
            }

            $configArray["servers"]["master"] = $this->getServerFromConfig($configArray["servers"]["master"]);
        }

        $this->configArray = $configArray;
    }

    /**
     * Gets a server object from a config array
     *
     * @param array $configArray The configuration array
     * @return SQL\Server The server object from the config options
     * @see serverIsValid() for the required config structure
     */
    protected function getServerFromConfig(array $configArray)
    {
        $server = new SQL\Server();
        $server->setHost($configArray["host"]);
        $server->setUsername($configArray["username"]);
        $server->setPassword($configArray["password"]);
        $server->setDatabaseName($configArray["databaseName"]);

        if(isset($configArray["port"]))
        {
            $server->setPort($configArray["port"]);
        }

        if(isset($configArray["charset"]))
        {
            $server->setCharset($configArray["charset"]);
        }

        return $server;
    }

    /**
     * {@inheritdoc}
     */
    protected function isValid(array $configArray)
    {
        if(!$this->hasRequiredFields($configArray, [
            "driver" => null,
            "servers" => [
                "master" => null
            ]
        ])
        )
        {
            return false;
        }

        /**
         * We don't validate the driver here because if it were a custom driver class, we'd have to instantiate it
         * to make sure it implements IDriver.  To save resources, we'll do this instantiation once in fromArray and
         * validate the resulting object there.
         */

        // Only accept server objects or valid server config arrays
        if(is_array($configArray["servers"]["master"]))
        {
            if(!$this->serverIsValid($configArray["servers"]["master"]))
            {
                return false;
            }
        }
        elseif(!$configArray["servers"]["master"] instanceof SQL\Server)
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
     *          "username" => server username credential,
     *          "password" => server password credential,
     *          "databaseName" => name of database on server to use
     *      The following keys are optional:
     *          "port" => server port,
     *          "charset" => character set
     * @return bool True if the config is valid, otherwise false
     */
    protected function serverIsValid(array $configArray)
    {
        if(!$this->hasRequiredFields($configArray, [
            "host" => null,
            "username" => null,
            "password" => null,
            "databaseName" => null,
        ])
        )
        {
            return false;
        }

        if(!is_string($configArray["host"]))
        {
            return false;
        }

        if(!is_string($configArray["username"]))
        {
            return false;
        }

        if(!is_string($configArray["password"]))
        {
            return false;
        }

        if(!is_string($configArray["databaseName"]))
        {
            return false;
        }

        if(isset($configArray["port"]) && !is_numeric($configArray["port"]))
        {
            return false;
        }

        if(isset($configArray["charset"]) && !is_string($configArray["charset"]))
        {
            return false;
        }

        return true;
    }
} 