<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the master/slave connection pool config
 */
namespace RDev\Models\Databases\SQL\Configs;
use RDev\Tests\Models\Databases\SQL\Mocks;

class MasterSlaveConnectionPoolConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests setting the driver as a fully-qualified driver class name
     */
    public function testSettingDriverWithAFullyQualifiedDriverName()
    {
        $config = new ConnectionPoolConfig([
            "driver" => "RDev\\Tests\\Models\\Databases\\SQL\\Mocks\\Driver",
            "servers" => [
                "master" => new Mocks\Server()
            ]
        ]);
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\IDriver", $config["driver"]);
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
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\IDriver", $config["driver"]);
    }

    /**
     * Tests using an array for the master and servers for the slave
     */
    public function testUsingArrayForMasterAndServersForSlave()
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
                    new Mocks\Server()
                ]
            ]
        ]);
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\Server", $config["servers"]["master"]);
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\Server", $config["servers"]["slaves"][0]);
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
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\Server", $config["servers"]["master"]);
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\Server", $config["servers"]["slaves"][0]);
    }

    /**
     * Tests using a server object for the master and an array for the slave
     */
    public function testUsingServerObjectForMasterAndArrayForSlave()
    {
        $config = new MasterSlaveConnectionPoolConfig([
            "driver" => new Mocks\Driver(),
            "servers" => [
                "master" => new Mocks\Server(),
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
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\Server", $config["servers"]["master"]);
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\Server", $config["servers"]["slaves"][0]);
    }
} 