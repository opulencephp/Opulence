<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the Redis server
 */
namespace RDev\Redis;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting if the password is set
     */
    public function testCheckingIfPasswordIsSet()
    {
        $server = new Server();
        $this->assertFalse($server->passwordIsSet());
        $server->setPassword("foo");
        $this->assertTrue($server->passwordIsSet());
    }

    /**
     * Tests getting the connection timeout
     */
    public function testGettingConnectionTimeout()
    {
        $server = new Server();
        $server->setConnectionTimeout(123);
        $this->assertEquals(123, $server->getConnectionTimeout());
    }

    /**
     * Tests getting the database index
     */
    public function testGettingDatabaseIndex()
    {
        $server = new Server();
        $server->setDatabaseIndex(100);
        $this->assertEquals(100, $server->getDatabaseIndex());
    }

    /**
     * Tests getting the password
     */
    public function testGettingPassword()
    {
        $server = new Server();
        $server->setPassword("foo");
        $this->assertEquals("foo", $server->getPassword());
    }

    /**
     * Tests getting the port
     */
    public function testGettingPort()
    {
        $server = new Server();
        $this->assertEquals(6379, $server->getPort());
    }

    /**
     * Tests setting the data in the constructor
     */
    public function testSettingDataInConstructor()
    {
        $server = new Server(
            "127.0.0.1",
            "password",
            123,
            1,
            60
        );
        $this->assertEquals("127.0.0.1", $server->getHost());
        $this->assertEquals("password", $server->getPassword());
        $this->assertEquals(123, $server->getPort());
        $this->assertEquals(1, $server->getDatabaseIndex());
        $this->assertEquals(60, $server->getConnectionTimeout());
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
} 