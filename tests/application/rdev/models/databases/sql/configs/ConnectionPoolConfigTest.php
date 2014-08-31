<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the connection pool config
 */
namespace RDev\Models\Databases\SQL\Configs;
use RDev\Tests\Models\Databases\SQL\Mocks;

class ConnectionPoolConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests a config that passes in a server object instead of an array of config settings
     */
    public function testConfigWithServerObject()
    {
        $server = new Mocks\Server();
        $configArray = $this->getValidConfigForServerTests();
        $configArray["servers"]["master"] = $server;
        $config = new ConnectionPoolConfig($configArray);
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\Server", $config["servers"]["master"]);
    }

    /**
     * Tests creating a server with no optional settings set
     */
    public function testCreatingServerWitNoOptionalSettingsSpecified()
    {
        $configArray = $this->getValidConfigForServerTests();
        $configArray["servers"]["master"] = [
            "host" => "127.0.0.1",
            "username" => "foo",
            "password" => "bar",
            "databaseName" => "mydb"
        ];
        $config = new ConnectionPoolConfig($configArray);
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\Server", $config["servers"]["master"]);
    }

    /**
     * Tests creating a server with an invalid charset specified
     */
    public function testCreatingServerWithInvalidCharsetSpecified()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = $this->getValidConfigForServerTests();
        $configArray["servers"]["master"] = [
            "host" => "127.0.0.1",
            "username" => "foo",
            "password" => "bar",
            "databaseName" => "mydb",
            "charset" => 123
        ];
        new ConnectionPoolConfig($configArray);
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
            "username" => "foo",
            "password" => "bar",
            "databaseName" => "mydb",
            "port" => "badport"
        ];
        new ConnectionPoolConfig($configArray);
    }

    /**
     * Tests creating a server without a character set specified
     */
    public function testCreatingServerWithoutCharsetSpecified()
    {
        $configArray = $this->getValidConfigForServerTests();
        $configArray["servers"]["master"] = [
            "host" => "127.0.0.1",
            "username" => "foo",
            "password" => "bar",
            "databaseName" => "mydb",
            "port" => 22
        ];
        $config = new ConnectionPoolConfig($configArray);
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\Server", $config["servers"]["master"]);
    }

    /**
     * Tests creating a server without a database name specified
     */
    public function testCreatingServerWithoutDatabaseNameSpecified()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = $this->getValidConfigForServerTests();
        $configArray["servers"]["master"] = [
            "host" => "127.0.0.1",
            "username" => "foo",
            "password" => "foo"
        ];
        new ConnectionPoolConfig($configArray);
    }

    /**
     * Tests creating a server without a host specified
     */
    public function testCreatingServerWithoutHostSpecified()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = $this->getValidConfigForServerTests();
        $configArray["servers"]["master"] = [
            "username" => "foo",
            "password" => "bar",
            "databaseName" => "mydb"
        ];
        new ConnectionPoolConfig($configArray);
    }

    /**
     * Tests creating a server without a password specified
     */
    public function testCreatingServerWithoutPasswordSpecified()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = $this->getValidConfigForServerTests();
        $configArray["servers"]["master"] = [
            "host" => "127.0.0.1",
            "username" => "foo",
            "databaseName" => "mydb"
        ];
        new ConnectionPoolConfig($configArray);
    }

    /**
     * Tests creating a server without a port specified
     */
    public function testCreatingServerWithoutPortSpecified()
    {
        $configArray = $this->getValidConfigForServerTests();
        $configArray["servers"]["master"] = [
            "host" => "127.0.0.1",
            "username" => "foo",
            "password" => "bar",
            "databaseName" => "mydb",
            "charset" => "utf8"
        ];
        $config = new ConnectionPoolConfig($configArray);
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\Server", $config["servers"]["master"]);
    }

    /**
     * Tests creating a server without a username specified
     */
    public function testCreatingServerWithoutUsernameSpecified()
    {
        $this->setExpectedException("\\RuntimeException");
        $configArray = $this->getValidConfigForServerTests();
        $configArray["servers"]["master"] = [
            "host" => "127.0.0.1",
            "password" => "bar",
            "databaseName" => "mydb"
        ];
        new ConnectionPoolConfig($configArray);
    }

    /**
     * Tests initializing the config without specifying a master
     */
    public function testNotSettingAMaster()
    {
        $this->setExpectedException("\\RuntimeException");
        new ConnectionPoolConfig([
            "driver" => new Mocks\Driver(),
            "servers" => []
        ]);
    }

    /**
     * Tests not setting the driver
     */
    public function testNotSettingDriver()
    {
        $this->setExpectedException("\\RuntimeException");
        new ConnectionPoolConfig(["servers" => []]);
    }

    /**
     * Tests not setting the servers
     */
    public function testNotSettingServers()
    {
        $this->setExpectedException("\\RuntimeException");
        new ConnectionPoolConfig(
            ["driver" => new Mocks\Driver()]
        );
    }

    /**
     * Tests setting the driver to a class that doesn't implement IDriver
     */
    public function testSettingDriverToClassThatDoesNotImplementIDriver()
    {
        $this->setExpectedException("\\RuntimeException");
        new ConnectionPoolConfig([
            "driver" => get_class($this),
            "servers" => [
                "master" => new Mocks\Server()
            ]
        ]);
    }

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
     * Tests setting the driver as a constant defined in the connection pool
     */
    public function testSettingDriverWithConstant()
    {
        $config = new ConnectionPoolConfig([
            "driver" => "pdo_pgsql",
            "servers" => [
                "master" => new Mocks\Server()
            ]
        ]);
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\IDriver", $config["driver"]);
    }

    /**
     * Tests setting the driver to a non-existent class
     */
    public function testSettingDriverWithNonExistentClass()
    {
        $this->setExpectedException("\\RuntimeException");
        new ConnectionPoolConfig([
            "driver" => "RDev\\Class\\That\\Does\\Not\\Exists",
            "servers" => [
                "master" => new Mocks\Server()
            ]
        ]);
    }

    /**
     * Tests initializing the config with an already-instantiated server
     */
    public function testUsingServerObject()
    {
        $config = new ConnectionPoolConfig([
            "driver" => new Mocks\Driver(),
            "servers" => [
                "master" => new Mocks\Server()
            ]
        ]);
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\Server", $config["servers"]["master"]);
    }

    /**
     * Tests initializing the config with just a master server
     */
    public function testWithJustAMaster()
    {
        $master = new Mocks\Server();
        $config = new ConnectionPoolConfig([
            "driver" => new Mocks\Driver(),
            "servers" => [
                "master" => new Mocks\Server()
            ]
        ]);
        $this->assertEquals($master, $config["servers"]["master"]);
    }

    /**
     * Gets a valid config that we can insert a server config into for testing
     *
     * @return array The config array
     */
    private function getValidConfigForServerTests()
    {
        return [
            "driver" => "pdo_pgsql",
            "servers" => [
                "master" => new Mocks\Server()
            ]
        ];
    }
} 