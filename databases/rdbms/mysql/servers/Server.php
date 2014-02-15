<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the base MySQL server object
 */
namespace RamODev\Databases\RDBMS\MySQL\Servers;
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
        return "mysql: unix_socket=/data/mysql/mysql.sock;host=" . $this->host . ";dbname=" . $this->databaseName . ";";
    }
} 