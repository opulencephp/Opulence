<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the server factory
 */
namespace RDev\Models\Databases\SQL;
use RDev\Tests\Models\Databases\SQL\Mocks;

class ServerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests a config that passes in a server object instead of an array of config settings
     */
    public function testConfigWithServerObject()
    {
        $server = new Mocks\Server();
        $config = $server;
        $factory = new ServerFactory();
        $this->assertInstanceOf("RDev\\Tests\\Models\\Databases\\SQL\\Mocks\\Server", $factory->createFromConfig($config));
    }

    /**
     * Tests creating a server with no optional settings set
     */
    public function testCreatingServerWitNoOptionalSettingsSpecified()
    {
        $config = [
            "host" => "127.0.0.1",
            "username" => "foo",
            "password" => "bar",
            "databaseName" => "mydb"
        ];
        $factory = new ServerFactory();
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\Server", $factory->createFromConfig($config));
    }

    /**
     * Tests creating a server with an invalid charset specified
     */
    public function testCreatingServerWithInvalidCharsetSpecified()
    {
        $this->setExpectedException("\\RuntimeException");
        $config = [
            "host" => "127.0.0.1",
            "username" => "foo",
            "password" => "bar",
            "databaseName" => "mydb",
            "charset" => 123
        ];
        (new ServerFactory())->createFromConfig($config);
    }

    /**
     * Tests creating a server with an invalid port specified
     */
    public function testCreatingServerWithInvalidPortSpecified()
    {
        $this->setExpectedException("\\RuntimeException");
        $config = [
            "host" => "127.0.0.1",
            "username" => "foo",
            "password" => "bar",
            "databaseName" => "mydb",
            "port" => "badport"
        ];
        (new ServerFactory())->createFromConfig($config);
    }

    /**
     * Tests creating a server without a character set specified
     */
    public function testCreatingServerWithoutCharsetSpecified()
    {
        $config = [
            "host" => "127.0.0.1",
            "username" => "foo",
            "password" => "bar",
            "databaseName" => "mydb",
            "port" => 22
        ];
        $factory = new ServerFactory();
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\Server", $factory->createFromConfig($config));
    }

    /**
     * Tests creating a server without a database name specified
     */
    public function testCreatingServerWithoutDatabaseNameSpecified()
    {
        $this->setExpectedException("\\RuntimeException");
        $config = [
            "host" => "127.0.0.1",
            "username" => "foo",
            "password" => "foo"
        ];
        (new ServerFactory())->createFromConfig($config);
    }

    /**
     * Tests creating a server without a host specified
     */
    public function testCreatingServerWithoutHostSpecified()
    {
        $this->setExpectedException("\\RuntimeException");
        $config = [
            "username" => "foo",
            "password" => "bar",
            "databaseName" => "mydb"
        ];
        (new ServerFactory())->createFromConfig($config);
    }

    /**
     * Tests creating a server without a password specified
     */
    public function testCreatingServerWithoutPasswordSpecified()
    {
        $this->setExpectedException("\\RuntimeException");
        $config = [
            "host" => "127.0.0.1",
            "username" => "foo",
            "databaseName" => "mydb"
        ];
        (new ServerFactory())->createFromConfig($config);
    }

    /**
     * Tests creating a server without a port specified
     */
    public function testCreatingServerWithoutPortSpecified()
    {
        $config = [
            "host" => "127.0.0.1",
            "username" => "foo",
            "password" => "bar",
            "databaseName" => "mydb",
            "charset" => "utf8"
        ];
        $factory = new ServerFactory();
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\Server", $factory->createFromConfig($config));
    }

    /**
     * Tests creating a server without a username specified
     */
    public function testCreatingServerWithoutUsernameSpecified()
    {
        $this->setExpectedException("\\RuntimeException");
        $config = [
            "host" => "127.0.0.1",
            "password" => "bar",
            "databaseName" => "mydb"
        ];
        (new ServerFactory())->createFromConfig($config);
    }
} 