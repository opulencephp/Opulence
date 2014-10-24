<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the Memcached server
 */
namespace RDev\Models\Databases\NoSQL\Memcached;
use RDev\Tests\Models\Databases\NoSQL\Memcached\Mocks;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the port
     */
    public function testGettingPort()
    {
        $server = new Mocks\Server();
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
     * Tests setting the weight
     */
    public function testSettingWeight()
    {
        $weight = 63;
        $server = new Mocks\Server();
        $server->setWeight($weight);
        $this->assertEquals($weight, $server->getWeight());
    }
} 