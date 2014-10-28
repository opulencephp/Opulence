<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the factory that instantiates single-server connection pools
 */
namespace RDev\Databases\SQL\Factories;
use RDev\Databases\SQL;
use RDev\Databases\SQL\Configs;

class SingleServerConnectionPoolFactory
{
    /**
     * Creates an instance of a single-server connection pool from a config
     *
     * @param Configs\ConnectionPoolConfig $config The config to instantiate from
     * @return SQL\SingleServerConnectionPool The instantiated connection pool
     */
    public function createFromConfig(Configs\ConnectionPoolConfig $config)
    {
        $driver = $config["driver"];
        $master = $config["servers"]["master"];
        $driverOptions = isset($config["driverOptions"]) ? $config["driverOptions"] : [];
        $connectionOptions = isset($config["connectionOptions"]) ? $config["connectionOptions"] : [];

        return new SQL\SingleServerConnectionPool($driver, $master, $driverOptions, $connectionOptions);
    }
} 