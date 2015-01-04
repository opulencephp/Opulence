<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the master/slave connection pool config
 */
namespace RDev\Databases\SQL\Configs;
use RDev\Databases\SQL;
use RDev\Tests\Databases\SQL\Mocks;

class MasterSlaveConnectionPoolConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests setting the driver as a fully-qualified driver class name
     */
    public function testSettingDriverWithAFullyQualifiedDriverName()
    {
        $config = new ConnectionPoolConfig([
            "driver" => "RDev\\Tests\\Databases\\SQL\\Mocks\\Driver",
            "servers" => [
                "master" => new Mocks\Server()
            ]
        ]);
        $this->assertInstanceOf("RDev\\Databases\\SQL\\IDriver", $config["driver"]);
    }

    /**
     * Tests setting the driver to an instantiated driver object
     */
    public function testSettingDriverWithAnInstantiatedDriverObject()
    {
        $config = new ConnectionPoolConfig([
            "driver" => new Mocks\Driver(),
            "servers" => [
                "master" => new Mocks\Server()
            ]
        ]);
        $this->assertInstanceOf("RDev\\Databases\\SQL\\IDriver", $config["driver"]);
    }

    /**
     * Tests using an array for the master and a server for the slave
     */
    public function testUsingArrayForMasterAndServerForSlave()
    {
        $slave = new Mocks\Server();
        $config = new MasterSlaveConnectionPoolConfig([
            "driver" => new Mocks\Driver(),
            "servers" => [
                "master" => [
                    "host" => "127.0.0.1",
                    "username" => "foo",
                    "password" => "bar",
                    "databaseName" => "mydb"
                ],
                "slaves" => [
                    $slave
                ]
            ]
        ]);
        /** @var SQL\Server $master */
        $master = $config["servers"]["master"];
        $this->assertInstanceOf("RDev\\Databases\\SQL\\Server", $master);
        $this->assertEquals("127.0.0.1", $master->getHost());
        $this->assertEquals("foo", $master->getUsername());
        $this->assertEquals("bar", $master->getPassword());
        $this->assertEquals("mydb", $master->getDatabaseName());
        $this->assertSame($slave, $config["servers"]["slaves"][0]);
    }

    /**
     * Tests using arrays for the master and a slave
     */
    public function testUsingArraysForMasterAndSlave()
    {
        $config = new MasterSlaveConnectionPoolConfig([
            "driver" => new Mocks\Driver(),
            "servers" => [
                "master" => [
                    "host" => "127.0.0.1",
                    "username" => "foo",
                    "password" => "bar",
                    "databaseName" => "mydb"
                ],
                "slaves" => [
                    [
                        "host" => "8.8.8.8",
                        "username" => "foo",
                        "password" => "bar",
                        "databaseName" => "mydb"
                    ]
                ]
            ]
        ]);
        /** @var SQL\Server $master */
        $master = $config["servers"]["master"];
        $this->assertInstanceOf("RDev\\Databases\\SQL\\Server", $master);
        $this->assertEquals("127.0.0.1", $master->getHost());
        $this->assertEquals("foo", $master->getUsername());
        $this->assertEquals("bar", $master->getPassword());
        $this->assertEquals("mydb", $master->getDatabaseName());
        /** @var SQL\Server $slave */
        $slave = $config["servers"]["slaves"][0];
        $this->assertInstanceOf("RDev\\Databases\\SQL\\Server", $slave);
        $this->assertEquals("8.8.8.8", $slave->getHost());
        $this->assertEquals("foo", $slave->getUsername());
        $this->assertEquals("bar", $slave->getPassword());
        $this->assertEquals("mydb", $slave->getDatabaseName());
    }

    /**
     * Tests using a server object for the master and an array for the slave
     */
    public function testUsingServerObjectForMasterAndArrayForSlave()
    {
        $master = new Mocks\Server();
        $config = new MasterSlaveConnectionPoolConfig([
            "driver" => new Mocks\Driver(),
            "servers" => [
                "master" => $master,
                "slaves" => [
                    [
                        "host" => "8.8.8.8",
                        "username" => "foo",
                        "password" => "bar",
                        "databaseName" => "mydb"
                    ]
                ]
            ]
        ]);
        $this->assertSame($master, $config["servers"]["master"]);
        /** @var SQL\Server $slave */
        $slave = $config["servers"]["slaves"][0];
        $this->assertInstanceOf("RDev\\Databases\\SQL\\Server", $slave);
        $this->assertEquals("8.8.8.8", $slave->getHost());
        $this->assertEquals("foo", $slave->getUsername());
        $this->assertEquals("bar", $slave->getPassword());
        $this->assertEquals("mydb", $slave->getDatabaseName());
    }
} 