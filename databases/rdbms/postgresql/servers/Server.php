<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the base PostgreSQL server object
 */
namespace RamODev\Databases\RDBMS\PostgreSQL\Servers;
use RamODev\Databases\RDBMS;

require_once(__DIR__ . "/../../Server.php");

abstract class Server extends RDBMS\Server
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