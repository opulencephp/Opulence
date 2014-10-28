<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the PDO driver
 */
namespace RDev\Databases\SQL\PDO;
use RDev\Tests\Databases\SQL\Mocks;

class DriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests connecting to a server
     */
    public function testConnectingToServer()
    {
        $server = new Mocks\Server();
        $driver = new Mocks\Driver();
        $this->assertInstanceOf("RDev\\Tests\\Databases\\SQL\\Mocks\\Connection", $driver->connect($server));
    }
} 