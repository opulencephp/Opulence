<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the driver class for use in testing
 */
namespace RDev\Tests\Models\Databases\SQL\Mocks;
use RDev\Models\Databases\SQL;

class Driver implements SQL\IDriver
{
    /**
     * {@inheritdoc}
     */
    public function connect(SQL\Server $server, array $connectionOptions = [], array $driverOptions = [])
    {
        return new Connection($server);
    }
} 