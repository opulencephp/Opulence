<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the base PostgreSQL server object
 */
namespace RamODev\Databases\SQL\PostgreSQL\Servers;
use RamODev\Databases\SQL;

abstract class Server extends SQL\Server
{
    /**
     * Gets the connection string
     *
     * @return string The string we can use to connect with
     */
    public function getConnectionString()
    {
        return "pgsql: host=" . $this->host . " dbname=" . $this->databaseName;
    }
} 