<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the driver class for use in testing
 */
namespace RDev\Tests\Databases\SQL\Mocks;
use RDev\Databases\SQL\IDriver;
use RDev\Databases\SQL\Server;

class Driver implements IDriver
{
    /**
     * {@inheritdoc}
     */
    public function connect(Server $server, array $connectionOptions = [], array $driverOptions = [])
    {
        return new Connection($server);
    }
} 