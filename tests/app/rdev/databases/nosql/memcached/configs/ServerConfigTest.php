<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the server config
 */
namespace RDev\Databases\NoSQL\Memcached\Configs;
use RDev\Databases\NoSQL\Memcached;

class ServerConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests creating a server with no optional settings set
     */
    public function testCreatingServerWitNoOptionalSettingsSpecified()
    {
        $configArray = $this->getValidConfigForServerTests();
        $configArray["servers"][] = [
            "host" => "127.0.0.1",
            "port" => 11211,
        ];
        $config = new ServerConfig($configArray);
        /** @var Memcached\Server $server */
        $server = $config["servers"][0];
        $this->assertInstanceOf("RDev\\Databases\\NoSQL\\Memcached\\Server", $server);
        $this->assertEquals("127.0.0.1", $server->getHost());
        $this->assertEquals(11211, $server->getPort());
    }

    /**
     * Tests creating a server with an invalid host specified
     */
    public function testCreatingServerWithInvalidHostSpecified()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = $this->getValidConfigForServerTests();
        $configArray["servers"][] = [
            "host" => 123,
            "port" => 11211
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
        $configArray["servers"][] = [
            "host" => "127.0.0.1",
            "port" => "badport"
        ];
        new ServerConfig($configArray);
    }

    /**
     * Tests creating a server with a weight specified
     */
    public function testCreatingServerWithWeightSpecified()
    {
        $configArray = $this->getValidConfigForServerTests();
        $configArray["servers"][] = [
            "host" => "127.0.0.1",
            "port" => 11211,
            "weight" => 50
        ];
        $config = new ServerConfig($configArray);
        /** @var Memcached\Server $server */
        $server = $config["servers"][0];
        $this->assertInstanceOf("RDev\\Databases\\NoSQL\\Memcached\\Server", $server);
        $this->assertEquals("127.0.0.1", $server->getHost());
        $this->assertEquals(11211, $server->getPort());
        $this->assertEquals(50, $server->getWeight());
    }

    /**
     * Tests creating a server without a host specified
     */
    public function testCreatingServerWithoutHostSpecified()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = $this->getValidConfigForServerTests();
        $configArray["servers"][] = [
            "port" => 11211,
        ];
        new ServerConfig($configArray);
    }

    /**
     * Tests creating a server without a port specified
     */
    public function testCreatingServerWithoutPortSpecified()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = $this->getValidConfigForServerTests();
        $configArray["servers"][] = [
            "host" => "127.0.0.1",
        ];
        new ServerConfig($configArray);
    }

    /**
     * Tests creating a server without a weight specified
     */
    public function testCreatingServerWithoutWeightSpecified()
    {
        $configArray = $this->getValidConfigForServerTests();
        $configArray["servers"][] = [
            "host" => "127.0.0.1",
            "port" => 11211
        ];
        $config = new ServerConfig($configArray);
        /** @var Memcached\Server $server */
        $server = $config["servers"][0];
        $this->assertInstanceOf("RDev\\Databases\\NoSQL\\Memcached\\Server", $server);
        $this->assertEquals("127.0.0.1", $server->getHost());
        $this->assertEquals(11211, $server->getPort());
    }

    /**
     * Tests initializing the config without specifying any server
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
     * Tests initializing the config with multiple arrays
     */
    public function testUsingMultipleArrays()
    {
        $config = new ServerConfig([
            "servers" => [
                [
                    "host" => "127.0.0.1",
                    "port" => 11211,
                    "weight" => 40
                ],
                [
                    "host" => "127.0.0.2",
                    "port" => 11211,
                    "weight" => 60
                ]
            ]
        ]);
        /** @var Memcached\Server $server1 */
        $server1 = $config["servers"][0];
        $this->assertInstanceOf("RDev\\Databases\\NoSQL\\Memcached\\Server", $server1);
        $this->assertEquals("127.0.0.1", $server1->getHost());
        $this->assertEquals(11211, $server1->getPort());
        $this->assertEquals(40, $server1->getWeight());
        /** @var Memcached\Server $server2 */
        $server2 = $config["servers"][1];
        $this->assertInstanceOf("RDev\\Databases\\NoSQL\\Memcached\\Server", $server2);
        $this->assertEquals("127.0.0.2", $server2->getHost());
        $this->assertEquals(11211, $server2->getPort());
        $this->assertEquals(60, $server2->getWeight());
    }

    /**
     * Tests initializing the config with multiple already-instantiated servers
     */
    public function testUsingMultipleServerObjects()
    {
        $server1 = new Memcached\Server();
        $server2 = new Memcached\Server();
        $config = new ServerConfig([
            "servers" => [
                $server1,
                $server2
            ]
        ]);
        $this->assertSame($server1, $config["servers"][0]);
        $this->assertSame($server2, $config["servers"][1]);
    }

    /**
     * Tests initializing the config with an already-instantiated server
     */
    public function testUsingServerObject()
    {
        $server = new Memcached\Server();
        $config = new ServerConfig([
            "servers" => [
                $server
            ]
        ]);
        $this->assertSame($server, $config["servers"][0]);
    }

    /**
     * Tests initializing the config with a mix of already-instantiated servers and arrays
     */
    public function testUsingServerObjectWithArray()
    {
        $server1 = new Memcached\Server();
        $config = new ServerConfig([
            "servers" => [
                $server1,
                [
                    "host" => "127.0.0.1",
                    "port" => 11211,
                    "weight" => 0
                ]
            ]
        ]);
        $this->assertSame($server1, $config["servers"][0]);
        /** @var Memcached\Server $server2 */
        $server2 = $config["servers"][1];
        $this->assertInstanceOf("RDev\\Databases\\NoSQL\\Memcached\\Server", $server2);
        $this->assertEquals("127.0.0.1", $server2->getHost());
        $this->assertEquals(11211, $server2->getPort());
        $this->assertEquals(0, $server2->getWeight());
    }

    /**
     * Gets a valid config that we can insert a server config into for testing
     *
     * @return array The config array
     */
    private function getValidConfigForServerTests()
    {
        return [
            "servers" => []
        ];
    }
}