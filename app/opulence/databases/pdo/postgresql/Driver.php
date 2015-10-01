<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the PDO driver for a PostgreSQL database
 */
namespace Opulence\Databases\PDO\PostgreSQL;

use Opulence\Databases\Providers\PostgreSQLProvider;
use Opulence\Databases\PDO\Driver as BaseDriver;
use Opulence\Databases\Server;

class Driver extends BaseDriver
{
    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    protected function setProvider()
    {
        $this->provider = new PostgreSQLProvider();
    }
} 