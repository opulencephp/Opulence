<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the PDO driver for a MySQL database
 */
namespace RDev\Models\Databases\SQL\PDO\MySQL;
use RDev\Models\Databases\SQL;
use RDev\Models\Databases\SQL\PDO;
use RDev\Models\Databases\SQL\Systems;

class Driver extends PDO\Driver
{
    /**
     * {@inheritdoc}
     */
    protected function createDSN(SQL\Server $server, array $options = [])
    {
        $dsn = "mysql: host=" . $server->getHost() . ";dbname=" . $server->getDatabaseName() . ";"
            . "port=" . $server->getPort() . ";charset=" . $server->getCharset() . ";";

        if(isset($options["unix_socket"]))
        {
            $dsn .= "unix_socket=" . $options["unix_socket"] . ";";
        }

        return $dsn;
    }

    /**
     * {@inheritdoc}
     */
    protected function setSystem()
    {
        $this->system = new Systems\MySQL();
    }
} 