<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the PDO driver for a PostgreSQL database
 */
namespace RDev\Databases\PDO\PostgreSQL;
use RDev\Databases\Providers\PostgreSQLProvider;
use RDev\Databases\PDO\Driver as BaseDriver;
use RDev\Databases\Server;

class Driver extends BaseDriver
{
    /**
     * {@inheritdoc}
     */
    protected function getDSN(Server $server, array $options = [])
    {
        $dsn = implode(";", [
                "pgsql:host=" . $server->getHost(),
                "dbname=" . $server->getDatabaseName(),
                "port=" . $server->getPort(),
                "options='--client_encoding=" . $server->getCharset() . "'"
            ]) . ";";

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
        $this->provider = new PostgreSQLProvider();
    }
} 