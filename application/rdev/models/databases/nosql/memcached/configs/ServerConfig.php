<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the server config
 */
namespace RDev\Models\Databases\NoSQL\Memcached\Configs;
use RDev\Models\Configs;
use RDev\Models\Databases\NoSQL\Memcached;

class ServerConfig extends Configs\Config
{
    /**
     * {@inheritdoc}
     */
    public function exchangeArray(array $configArray)
    {
        if(!$this->isValid($configArray))
        {
            throw new \RuntimeException("Invalid Memcached config");
        }

        foreach($configArray["servers"] as &$serverConfig)
        {
            if(!$serverConfig instanceof Memcached\Server)
            {
                $server = new Memcached\Server();
                $server->setHost($serverConfig["host"]);
                $server->setPort($serverConfig["port"]);

                if(isset($serverConfig["weight"]))
                {
                    $server->setWeight($serverConfig["weight"]);
                }

                $serverConfig = $server;
            }
        }

        $this->configArray = $configArray;
    }

    /**
     * {@inheritdoc}
     */
    protected function isValid(array $configArray)
    {
        if(!$this->hasRequiredFields($configArray, [
            "servers" => null
        ])
        )
        {
            return false;
        }

        if(!is_array($configArray["servers"]))
        {
            return false;
        }

        if(count($configArray["servers"]) == 0)
        {
            return false;
        }

        foreach($configArray["servers"] as $server)
        {
            // Only accept server objects or valid server config arrays
            if(is_array($server))
            {
                if(!$this->serverIsValid($server))
                {
                    return false;
                }
            }
            elseif(!$server instanceof Memcached\Server)
            {
                return false;
            }
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
     *          "weight" => weight of this server relative to the total weights of all the servers
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

        if(isset($configArray["weight"]) && !is_numeric($configArray["weight"]))
        {
            return false;
        }

        return true;
    }
} 