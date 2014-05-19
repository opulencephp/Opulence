<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the base MySQL server object
 */
namespace RDev\Models\Databases\SQL\MySQL;
use RDev\Models\Databases\SQL;

abstract class Server extends SQL\Server
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