<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the Redis server
 */
namespace RDev\Models\Databases\NoSQL\Redis;
use RDev\Tests\Models\Databases\NoSQL\Redis\Mocks;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting if the password is set
     */
    public function testCheckingIfPasswordIsSet()
    {
        $server = new Mocks\Server();
        $this->assertFalse($server->passwordIsSet());
        $server->setPassword("foo");
        $this->assertTrue($server->passwordIsSet());
    }

    /**
     * Tests getting the connection timeout
     */
    public function testGettingConnectionTimeout()
    {
        $server = new Mocks\Server();
        $server->setConnectionTimeout(123);
        $this->assertEquals(123, $server->getConnectionTimeout());
    }

    /**
     * Tests getting the database index
     */
    public function testGettingDatabaseIndex()
    {
        $server = new Mocks\Server();
        $server->setDatabaseIndex(100);
        $this->assertEquals(100, $server->getDatabaseIndex());
    }

    /**
     * Tests getting the password
     */
    public function testGettingPassword()
    {
        $server = new Mocks\Server();
        $server->setPassword("foo");
        $this->assertEquals("foo", $server->getPassword());
    }

    /**
     * Tests getting the port
     */
    public function testGettingPort()
    {
        $server = new Mocks\Server();
        $this->assertEquals(6379, $server->getPort());
    }
} 