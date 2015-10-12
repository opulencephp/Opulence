<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the PDO driver
 */
namespace Opulence\Databases\PDO;

use Opulence\Tests\Databases\Mocks\Connection;
use Opulence\Tests\Databases\Mocks\Driver;
use Opulence\Tests\Databases\Mocks\Server;

class DriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests connecting to a server
     */
    public function testConnectingToServer()
    {
        $server = new Server();
        $driver = new Driver();
        $this->assertInstanceOf(Connection::class, $driver->connect($server));
    }
} 