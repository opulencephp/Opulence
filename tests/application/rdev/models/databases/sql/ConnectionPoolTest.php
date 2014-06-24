<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the connection pool
 */
namespace RDev\Models\Databases\SQL;
use RDev\Tests\Models\Databases\SQL\Mocks;

class ConnectionPoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests initializing the pool without specifying a master
     */
    public function testNotSettingAMaster()
    {
        $this->setExpectedException("\\RuntimeException");
        $config = [
            "driver" => new Mocks\Driver(),
            "servers" => []
        ];
        $connectionPool = new Mocks\ConnectionPool($config);
    }

    /**
     * Tests not setting the driver
     */
    public function testNotSettingDriver()
    {
        $this->setExpectedException("\\RuntimeException");
        $connectionPool = new Mocks\ConnectionPool(["servers" => []]);
    }

    /**
     * Tests not setting the servers
     */
    public function testNotSettingServers()
    {
        $this->setExpectedException("\\RuntimeException");
        $connectionPool = new Mocks\ConnectionPool(["driver" => new Mocks\Driver()]);
    }

    /**
     * Tests setting the driver as a fully-qualified driver class name
     */
    public function testSettingDriverWithAFullyQualifiedDriverName()
    {
        $config = [
            "driver" => "RDev\\Tests\\Models\\Databases\\SQL\\Mocks\\Driver",
            "servers" => [
                "master" => [
                    "host" => "127.0.0.1",
                    "username" => "foo",
                    "password" => "bar",
                    "databaseName" => "mydb"
                ]
            ]
        ];
        $connectionPool = new Mocks\ConnectionPool($config);
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\IDriver", $connectionPool->getDriver());
    }

    /**
     * Tests setting the driver as an instantiated driver object
     */
    public function testSettingDriverWithAnInstantiatedDriverObject()
    {
        $config = [
            "driver" => new Mocks\Driver(),
            "servers" => [
                "master" => [
                    "host" => "127.0.0.1",
                    "username" => "foo",
                    "password" => "bar",
                    "databaseName" => "mydb"
                ]
            ]
        ];
        $connectionPool = new Mocks\ConnectionPool($config);
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\IDriver", $connectionPool->getDriver());
    }

    /**
     * Tests setting the driver as a constant defined in the connection pool
     */
    public function testSettingDriverWithConstant()
    {
        $config = [
            "driver" => "pdo_postgresql",
            "servers" => [
                "master" => [
                    "host" => "127.0.0.1",
                    "username" => "foo",
                    "password" => "bar",
                    "databaseName" => "mydb"
                ]
            ]
        ];
        $connectionPool = new Mocks\ConnectionPool($config);
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\IDriver", $connectionPool->getDriver());
    }

    /**
     * Tests setting the driver to a non-existent class
     */
    public function testSettingDriverWithNonExistentClass()
    {
        $this->setExpectedException("\\RuntimeException");
        $config = [
            "driver" => "RDev\\Class\\That\\Does\\Not\\Exists",
            "servers" => [
                "master" => [
                    "host" => "127.0.0.1",
                    "username" => "foo",
                    "password" => "bar",
                    "databaseName" => "mydb"
                ]
            ]
        ];
        $connectionPool = new Mocks\ConnectionPool($config);
    }

    /**
     * Tests setting the master
     */
    public function testSettingMaster()
    {
        $config = [
            "driver" => new Mocks\Driver(),
            "servers" => [
                "master" => [
                    "host" => "127.0.0.1",
                    "username" => "foo",
                    "password" => "bar",
                    "databaseName" => "mydb"
                ]
            ]
        ];
        $connectionPool = new Mocks\ConnectionPool($config);
        $master = new Mocks\Server();
        $connectionPool->setMaster($master);
        $this->assertEquals($master, $connectionPool->getMaster());
    }

    /**
     * Tests initializing the pool with an already-instantiated server
     */
    public function testUsingServerObject()
    {
        $config = [
            "driver" => new Mocks\Driver(),
            "servers" => [
                "master" => new Mocks\Server()
            ]
        ];
        $connectionPool = new Mocks\ConnectionPool($config);
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\Server", $connectionPool->getMaster());
    }

    /**
     * Tests initializing the pool with just a master server
     */
    public function testWithJustAMaster()
    {
        $config = [
            "driver" => new Mocks\Driver(),
            "servers" => [
                "master" => [
                    "host" => "127.0.0.1",
                    "username" => "foo",
                    "password" => "bar",
                    "databaseName" => "mydb"
                ]
            ]
        ];
        $connectionPool = new Mocks\ConnectionPool($config);
        $this->assertEquals("127.0.0.1", $connectionPool->getMaster()->getHost());
    }
} 