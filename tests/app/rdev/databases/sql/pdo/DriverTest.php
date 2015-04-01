<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the PDO driver
 */
namespace RDev\Databases\SQL\PDO;
use RDev\Tests\Databases\SQL\Mocks\Driver;
use RDev\Tests\Databases\SQL\Mocks\Server;

class DriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests connecting to a server
     */
    public function testConnectingToServer()
    {
        $server = new Server();
        $driver = new Driver();
        $this->assertInstanceOf("RDev\\Tests\\Databases\\SQL\\Mocks\\Connection", $driver->connect($server));
    }
} 