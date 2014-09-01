<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the server config
 */
namespace RDev\Models\Databases\NoSQL\Redis\Configs;
use RDev\Models\Databases\NoSQL\Redis;

class ServerConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests creating a server with no optional settings set
     */
    public function testCreatingServerWitNoOptionalSettingsSpecified()
    {
        $configArray = $this->getValidConfigForServerTests();
        $configArray["servers"]["master"] = [
            "host" => "127.0.0.1",
            "port" => 6379,
        ];
        $config = new ServerConfig($configArray);
        /** @var Redis\Server $master */
        $master = $config["servers"]["master"];
        $this->assertInstanceOf("RDev\\Models\\Databases\\NoSQL\\Redis\\Server", $master);
        $this->assertEquals("127.0.0.1", $master->getHost());
        $this->assertEquals(6379, $master->getPort());
    }

    /**
     * Tests creating a server with an invalid host specified
     */
    public function testCreatingServerWithInvalidHostSpecified()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = $this->getValidConfigForServerTests();
        $configArray["servers"]["master"] = [
            "host" => 123,
            "port" => 6379
        ];
        new ServerConfig($configArray);
    }

    /**
     * Tests creating a server with an invalid port specified
     */
    public function testCreatingServerWithInvalidPortSpecified()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = $this->getValidConfigForServerTests();
        $configArray["servers"]["master"] = [
            "host" => "127.0.0.1",
            "port" => "badport"
        ];
        new ServerConfig($configArray);
    }

    /**
     * Tests creating a server without a connection timeout specified
     */
    public function testCreatingServerWithoutConnectionTimeoutSpecified()
    {
        $configArray = $this->getValidConfigForServerTests();
        $configArray["servers"]["master"] = [
            "host" => "127.0.0.1",
            "port" => 6379,
            "password" => "foo",
            "databaseIndex" => 0
        ];
        $config = new ServerConfig($configArray);
        /** @var Redis\Server $master */
        $master = $config["servers"]["master"];
        $this->assertInstanceOf("RDev\\Models\\Databases\\NoSQL\\Redis\\Server", $master);
        $this->assertEquals("127.0.0.1", $master->getHost());
        $this->assertEquals(6379, $master->getPort());
        $this->assertEquals("foo", $master->getPassword());
        $this->assertEquals(0, $master->getDatabaseIndex());
    }

    /**
     * Tests creating a server without a database index specified
     */
    public function testCreatingServerWithoutDatabaseIndexSpecified()
    {
        $configArray = $this->getValidConfigForServerTests();
        $configArray["servers"]["master"] = [
            "host" => "127.0.0.1",
            "port" => 6379,
            "password" => "foo",
            "connectionTimeout" => 5
        ];
        $config = new ServerConfig($configArray);
        /** @var Redis\Server $master */
        $master = $config["servers"]["master"];
        $this->assertInstanceOf("RDev\\Models\\Databases\\NoSQL\\Redis\\Server", $master);
        $this->assertEquals("127.0.0.1", $master->getHost());
        $this->assertEquals(6379, $master->getPort());
        $this->assertEquals("foo", $master->getPassword());
        $this->assertEquals(5, $master->getConnectionTimeout());
    }

    /**
     * Tests creating a server without a host specified
     */
    public function testCreatingServerWithoutHostSpecified()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = $this->getValidConfigForServerTests();
        $configArray["servers"]["master"] = [
            "port" => 6379,
        ];
        new ServerConfig($configArray);
    }

    /**
     * Tests creating a server without a password set specified
     */
    public function testCreatingServerWithoutPasswordSpecified()
    {
        $configArray = $this->getValidConfigForServerTests();
        $configArray["servers"]["master"] = [
            "host" => "127.0.0.1",
            "port" => 6379,
            "databaseIndex" => 0,
            "connectionTimeout" => 5
        ];
        $config = new ServerConfig($configArray);
        /** @var Redis\Server $master */
        $master = $config["servers"]["master"];
        $this->assertInstanceOf("RDev\\Models\\Databases\\NoSQL\\Redis\\Server", $master);
        $this->assertEquals("127.0.0.1", $master->getHost());
        $this->assertEquals(6379, $master->getPort());
        $this->assertEquals(0, $master->getDatabaseIndex());
        $this->assertEquals(5, $master->getConnectionTimeout());
    }

    /**
     * Tests creating a server without a port specified
     */
    public function testCreatingServerWithoutPortSpecified()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = $this->getValidConfigForServerTests();
        $configArray["servers"]["master"] = [
            "host" => "127.0.0.1",
        ];
        new ServerConfig($configArray);
    }

    /**
     * Tests initializing the config without specifying a master
     */
    public function testNotSettingAMaster()
    {
        $this->setExpectedException("\\RuntimeException");
        new ServerConfig([
            "servers" => []
        ]);
    }

    /**
     * Tests not setting the servers
     */
    public function testNotSettingServers()
    {
        $this->setExpectedException("\\RuntimeException");
        new ServerConfig([]);
    }

    /**
     * Tests initializing the config with an already-instantiated server
     */
    public function testUsingServerObject()
    {
        $master = new Redis\Server();
        $config = new ServerConfig([
            "servers" => [
                "master" => $master
            ]
        ]);
        $this->assertSame($master, $config["servers"]["master"]);
    }

    /**
     * Gets a valid config that we can insert a server config into for testing
     *
     * @return array The config array
     */
    private function getValidConfigForServerTests()
    {
        return [
            "servers" => [
                "master" => null
            ]
        ];
    }
} 