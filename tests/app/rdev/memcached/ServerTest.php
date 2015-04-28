<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the Memcached server
 */
namespace RDev\Memcached;
use RDev\Tests\Databases\NoSQL\Memcached\Mocks\Server;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the port
     */
    public function testGettingPort()
    {
        $server = new Server();
        $this->assertEquals(11211, $server->getPort());
    }

    /**
     * Tests setting the data in the constructor
     */
    public function testSettingDataInConstructor()
    {
        $server = new Server("127.0.0.1", 123, 55);
        $this->assertEquals("127.0.0.1", $server->getHost());
        $this->assertEquals(123, $server->getPort());
        $this->assertEquals(55, $server->getWeight());
    }

    /**
     * Tests setting the host
     */
    public function testSettingHost()
    {
        $server = new Server();
        $server->setHost("127.0.0.1");
        $this->assertEquals("127.0.0.1", $server->getHost());
    }

    /**
     * Tests setting the port
     */
    public function testSettingPort()
    {
        $server = new Server();
        $server->setPort(80);
        $this->assertEquals(80, $server->getPort());
    }

    /**
     * Tests setting the weight
     */
    public function testSettingWeight()
    {
        $weight = 63;
        $server = new Server();
        $server->setWeight($weight);
        $this->assertEquals($weight, $server->getWeight());
    }
} 