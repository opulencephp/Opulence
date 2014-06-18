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
     * Tests getting the read connection without setting a master
     */
    public function testGettingReadConnectionWithoutSettingMaster()
    {
        $this->setExpectedException("\\RuntimeException");
        $connectionPool = $this->getConnectionPool();
        $connectionPool->getReadConnection();
    }

    /**
     * Tests getting the write connection without setting a master
     */
    public function testGettingWriteConnectionWithoutSettingMaster()
    {
        $this->setExpectedException("\\RuntimeException");
        $connectionPool = $this->getConnectionPool();
        $connectionPool->getWriteConnection();
    }

    /**
     * Tests initializing the pool with just a master server
     */
    public function testInitializingFromConfigWithJustAMaster()
    {
        $connectionPool = $this->getConnectionPool();
        $config = [
            "master" => [
                "host" => "127.0.0.1",
                "username" => "foo",
                "password" => "bar",
                "databaseName" => "mydb"
            ]
        ];
        $connectionPool->initFromConfig($config);
        $this->assertEquals("127.0.0.1", $connectionPool->getMaster()->getHost());
    }

    /**
     * Tests initializing the pool with an already-instantiated server
     */
    public function testInitializingFromConfigWithServerObject()
    {
        $connectionPool = $this->getConnectionPool();
        $config = [
            "master" => new Mocks\Server()
        ];
        $connectionPool->initFromConfig($config);
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\Server", $connectionPool->getMaster());
    }

    /**
     * Tests initializing the pool without specifying a master
     */
    public function testInitializingFromConfigWithoutMaster()
    {
        $this->setExpectedException("\\RuntimeException");
        $connectionPool = $this->getConnectionPool();
        $config = [];
        $connectionPool->initFromConfig($config);
    }

    /**
     * Tests setting the master
     */
    public function testSettingMaster()
    {
        $master = new Mocks\Server();
        $connectionPool = $this->getConnectionPool();
        $connectionPool->setMaster($master);
        $this->assertEquals($master, $connectionPool->getMaster());
    }

    /**
     * Gets the connection pool object to use in tests
     *
     * @return ConnectionPool The connection pool object to use in tests
     */
    private function getConnectionPool()
    {
        $driver = new Mocks\Driver();
        $connectionFactory = new ConnectionFactory($driver);

        return new Mocks\ConnectionPool($connectionFactory);
    }
} 