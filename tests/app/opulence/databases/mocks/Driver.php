<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the driver class for use in testing
 */
namespace Opulence\Tests\Databases\Mocks;

use Opulence\Databases\IDriver;
use Opulence\Databases\Server as BaseServer;

class Driver implements IDriver
{
    /**
     * @inheritdoc
     */
    public function connect(BaseServer $server, array $connectionOptions = [], array $driverOptions = [])
    {
        return new Connection($server);
    }
} 