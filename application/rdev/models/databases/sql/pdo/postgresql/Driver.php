<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the PDO driver for a PostgreSQL database
 */
namespace RDev\Models\Databases\SQL\PDO\PostgreSQL;
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
            . "port=" . $server->getPort() . ";options='--client_encoding=" . $server->getCharset() . "';";

        if(isset($options["sslmode"]))
        {
            $dsn .= "sslmode=" . $options["sslmode"] . ";";
        }

        return $dsn;
    }

    /**
     * {@inheritdoc}
     */
    protected function setSystem()
    {
        $this->system = new Systems\PostgreSQL();
    }
} 