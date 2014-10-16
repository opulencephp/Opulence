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
     * Tests getting the read connection without a preferred server
     */
    public function testGettingReadConnection()
    {
        $connectionPool = $this->getConnectionPool();
        $master = new Mocks\Server();
        $expectedConnection = new Mocks\Connection($master);
        $connectionPool->setMaster($master);
        $this->assertEquals($expectedConnection, $connectionPool->getReadConnection());
    }

    /**
     * Tests getting the read connection with a preferred server
     */
    public function testGettingReadConnectionWithPreferredServer()
    {
        $connectionPool = $this->getConnectionPool();
        $preferredServer = new Mocks\Server();
        $expectedConnection = new Mocks\Connection($preferredServer);
        $this->assertEquals($expectedConnection, $connectionPool->getReadConnection($preferredServer));
    }

    /**
     * Tests getting the write connection without a preferred server
     */
    public function testGettingWriteConnection()
    {
        $connectionPool = $this->getConnectionPool();
        $master = new Mocks\Server();
        $expectedConnection = new Mocks\Connection($master);
        $connectionPool->setMaster($master);
        $this->assertEquals($expectedConnection, $connectionPool->getWriteConnection());
    }

    /**
     * Tests getting the write connection with a preferred server
     */
    public function testGettingWriteConnectionWithPreferredServer()
    {
        $connectionPool = $this->getConnectionPool();
        $preferredServer = new Mocks\Server();
        $expectedConnection = new Mocks\Connection($preferredServer);
        $this->assertEquals($expectedConnection, $connectionPool->getWriteConnection($preferredServer));
    }

    /**
     * Gets a connection pool to use in the tests
     *
     * @return SingleServerConnectionPool The connection pool to use
     */
    private function getConnectionPool()
    {
        $configArray = [
            "driver" => new Mocks\Driver(),
            "servers" => [
                "master" => new Mocks\Server()
            ]
        ];
        $config = new Configs\ConnectionPoolConfig($configArray);
        $singleServerConnectionPoolFactory = new Factories\SingleServerConnectionPoolFactory();

        return $singleServerConnectionPoolFactory->createFromConfig($config);
    }
}