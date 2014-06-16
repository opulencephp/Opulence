<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the connection factory
 */
namespace RDev\Models\Databases\SQL;
use RDev\Tests\Models\Databases\SQL\Mocks;

class ConnectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests connecting to a server
     */
    public function testConnecting()
    {
        $driver = new Mocks\Driver();
        $server = new Mocks\Server();
        $factory = new ConnectionFactory($driver);
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\IConnection", $factory->connect($server));
    }
} 