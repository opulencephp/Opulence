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
        $factory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $connectionPool = new MasterSlaveConnectionPool($factory, $master);
        $this->assertEquals($master, $connectionPool->getMaster());
    }

    /**
     * Tests getting the read connection without a preferred server
     */
    public function testGettingReadConnection()
    {
        $factory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $expectedConnection = new Mocks\Connection($master);
        $connectionPool = new SingleServerConnectionPool($factory, $master);
        $this->assertEquals($expectedConnection, $connectionPool->getReadConnection());
    }

    /**
     * Tests getting the read connection with a preferred server
     */
    public function testGettingReadConnectionWithPreferredServer()
    {
        $factory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $preferredServer = new Mocks\Server();
        $expectedConnection = new Mocks\Connection($preferredServer);
        $connectionPool = new SingleServerConnectionPool($factory, $master);
        $this->assertEquals($expectedConnection, $connectionPool->getReadConnection($preferredServer));
    }

    /**
     * Tests getting the write connection without a preferred server
     */
    public function testGettingWriteConnection()
    {
        $factory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $expectedConnection = new Mocks\Connection($master);
        $connectionPool = new SingleServerConnectionPool($factory, $master);
        $this->assertEquals($expectedConnection, $connectionPool->getWriteConnection());
    }

    /**
     * Tests getting the write connection with a preferred server
     */
    public function testGettingWriteConnectionWithPreferredServer()
    {
        $factory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $preferredServer = new Mocks\Server();
        $expectedConnection = new Mocks\Connection($preferredServer);
        $connectionPool = new SingleServerConnectionPool($factory, $master);
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