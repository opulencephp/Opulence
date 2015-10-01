<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the PDO driver for a MySQL database
 */
namespace Opulence\Databases\PDO\MySQL;

use Opulence\Databases\Providers\MySQLProvider;
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
                "mysql:host=" . $server->getHost(),
                "dbname=" . $server->getDatabaseName(),
                "port=" . $server->getPort(),
                "charset=" . $server->getCharset()
            ]) . ";";

        if(isset($options["unix_socket"]))
        {
            $dsn .= "unix_socket=" . $options["unix_socket"] . ";";
        }

        return $dsn;
    }

    /**
     * @inheritdoc
     */
    protected function setProvider()
    {
        $this->provider = new MySQLProvider();
    }
} 