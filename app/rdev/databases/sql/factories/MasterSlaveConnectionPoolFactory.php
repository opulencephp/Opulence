<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the factory that instantiates master-slave connection pools
 */
namespace RDev\Databases\SQL\Factories;
use RDev\Databases\SQL;
use RDev\Databases\SQL\Configs;

class MasterSlaveConnectionPoolFactory
{
    /**
     * Creates an instance of a master-slave connection pool from a config
     *
     * @param Configs\MasterSlaveConnectionPoolConfig $config The config to instantiate from
     * @return SQL\MasterSlaveConnectionPool The instantiated connection pool
     */
    public function createFromConfig(Configs\MasterSlaveConnectionPoolConfig $config)
    {
        $driver = $config["driver"];
        $master = $config["servers"]["master"];
        $slaves = isset($config["servers"]["slaves"]) ? $config["servers"]["slaves"] : [];
        $driverOptions = isset($config["driverOptions"]) ? $config["driverOptions"] : [];
        $connectionOptions = isset($config["connectionOptions"]) ? $config["connectionOptions"] : [];

        return new SQL\MasterSlaveConnectionPool($driver, $master, $slaves, $driverOptions, $connectionOptions);
    }
} 