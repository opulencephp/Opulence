<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the driver class for use in testing
 */
namespace Opulence\Tests\Databases\SQL\Mocks;
use Opulence\Databases\IDriver;
use Opulence\Databases\Server;

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