<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the PDO driver for a MySQL database
 */
namespace RDev\Databases\SQL\PDO\MySQL;
use RDev\Databases\SQL\Server;
use RDev\Databases\SQL\PDO\Driver as BaseDriver;
use RDev\Databases\SQL\Providers\MySQLProvider;

class Driver extends BaseDriver
{
    /**
     * {@inheritdoc}
     */
    protected function getDSN(Server $server, array $options = [])
    {
        $dsn = "mysql:host=" . $server->getHost() . ";dbname=" . $server->getDatabaseName() . ";"
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
    protected function setProvider()
    {
        $this->provider = new MySQLProvider();
    }
} 