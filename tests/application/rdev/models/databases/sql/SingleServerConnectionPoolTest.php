<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the single server connection pool
 */
namespace RDev\Models\Databases\SQL;
use RDev\Tests\Models\Databases\SQL\Mocks;

class SingleServerConnectionPoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the master after setting it in the constructor
     */
    public function testGettingMasterAfterSettingInConstructor()
    {
        $connectionFactory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $connectionPoolPool = new MasterSlaveConnectionPool($connectionFactory, $master);
        $this->assertEquals($master, $connectionPoolPool->getMaster());
    }

    /**
     * Tests getting the read connection without a preferred server
     */
    public function testGettingReadConnection()
    {
        $connectionFactory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $expectedConnection = new Mocks\Connection($master);
        $connectionPool = new SingleServerConnectionPool($connectionFactory, $master);
        $this->assertEquals($expectedConnection, $connectionPool->getReadConnection());
    }

    /**
     * Tests getting the read connection with a preferred server
     */
    public function testGettingReadConnectionWithPreferredServer()
    {
        $connectionFactory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $preferredServer = new Mocks\Server();
        $expectedConnection = new Mocks\Connection($preferredServer);
        $connectionPool = new SingleServerConnectionPool($connectionFactory, $master);
        $this->assertEquals($expectedConnection, $connectionPool->getReadConnection($preferredServer));
    }

    /**
     * Tests getting the write connection without a preferred server
     */
    public function testGettingWriteConnection()
    {
        $connectionFactory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $expectedConnection = new Mocks\Connection($master);
        $connectionPool = new SingleServerConnectionPool($connectionFactory, $master);
        $this->assertEquals($expectedConnection, $connectionPool->getWriteConnection());
    }

    /**
     * Tests getting the write connection with a preferred server
     */
    public function testGettingWriteConnectionWithPreferredServer()
    {
        $connectionFactory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $preferredServer = new Mocks\Server();
        $expectedConnection = new Mocks\Connection($preferredServer);
        $connectionPool = new SingleServerConnectionPool($connectionFactory, $master);
        $this->assertEquals($expectedConnection, $connectionPool->getWriteConnection($preferredServer));
    }

    /**
     * Gets a connection factory to use in the tests
     *
     * @return ConnectionFactory The connection factory to use
     */
    private function getConnectionFactory()
    {
        $driver = new Mocks\Driver();

        return new ConnectionFactory($driver);
    }
} 