<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the PDO driver for a PostgreSQL database
 */
namespace RDev\Databases\SQL\PDO\PostgreSQL;
use RDev\Databases\SQL;
use RDev\Databases\SQL\PDO;
use RDev\Databases\SQL\Providers;

class Driver extends PDO\Driver
{
    /**
     * {@inheritdoc}
     */
    protected function getDSN(SQL\Server $server, array $options = [])
    {
        $dsn = "pgsql: host=" . $server->getHost() . ";dbname=" . $server->getDatabaseName() . ";"
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
    protected function setProvider()
    {
        $this->provider = new Providers\PostgreSQL();
    }
} 