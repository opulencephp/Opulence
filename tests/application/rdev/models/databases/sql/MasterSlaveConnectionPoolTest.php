<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the master/slave connection pool
 */
namespace RDev\Models\Databases\SQL;
use RDev\Tests\Models\Databases\SQL\Mocks;

class MasterSlaveConnectionPoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding a slave
     */
    public function testAddingSlave()
    {
        $slave = $this->createServer();
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $this->createServer());
        $connectionPool->addSlave($slave);
        $this->assertEquals([$slave], $connectionPool->getSlaves());
    }

    /**
     * Tests adding slaves
     */
    public function testAddingSlaves()
    {
        $slave1 = $this->createServer();
        $slave2 = $this->createServer();
        $slave3 = $this->createServer();
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $this->createServer(), [$slave1]);
        $connectionPool->addSlaves([$slave2, $slave3]);
        $this->assertEquals([$slave1, $slave2, $slave3], $connectionPool->getSlaves());
    }

    /**
     * Tests creating a connection with no slaves
     */
    public function testCreatingConnectionWithNoSlaves()
    {
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $this->createServer());
        $this->assertEquals([], $connectionPool->getSlaves());
    }

    /**
     * Tests getting the read connection with no slaves
     */
    public function testGettingReadConnectionWithNoSlaves()
    {
        $master = $this->createServer();
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $master);
        $expectedConnection = new Mocks\Connection($master);
        $this->assertEquals($expectedConnection, $connectionPool->getReadConnection());
    }

    /**
     * Tests getting the read connection with a preferred server
     */
    public function testGettingReadConnectionWithPreferredServer()
    {
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $this->createServer());
        $preferredServer = new Mocks\Server();
        $expectedConnection = new Mocks\Connection($preferredServer);
        $this->assertEquals($expectedConnection, $connectionPool->getReadConnection($preferredServer));
    }

    /**
     * Tests getting the read connection with slaves
     */
    public function testGettingReadConnectionWithSlaves()
    {
        $slave1 = $this->createServer();
        $slave2 = $this->createServer();
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $this->createServer(), [$slave1, $slave2]);
        $expectedServers = [$slave1, $slave2];
        $expectedPDO = $connectionPool->getReadConnection();
        $slaveFound = false;

        foreach($expectedServers as $server)
        {
            if($expectedPDO->getServer() == $server)
            {
                $slaveFound = true;
            }
        }

        $this->assertTrue($slaveFound);
    }

    /**
     * Tests getting the write connection with no slaves
     */
    public function testGettingWriteConnectionWithNoSlaves()
    {
        $master = $this->createServer();
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $master);
        $expectedConnection = new Mocks\Connection($master);
        $this->assertEquals($expectedConnection, $connectionPool->getWriteConnection());
    }

    /**
     * Tests getting the write connection with a preferred server
     */
    public function testGettingWriteConnectionWithPreferredServer()
    {
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $this->createServer());
        $preferredServer = $this->createServer();
        $expectedConnection = new Mocks\Connection($preferredServer);
        $this->assertEquals($expectedConnection, $connectionPool->getWriteConnection($preferredServer));
    }

    /**
     * Tests removing a slave
     */
    public function testRemovingSlave()
    {
        $slave1 = $this->createServer();
        $slave1->setDatabaseName("slave1");
        $slave2 = $this->createServer();
        $slave2->setDatabaseName("slave2");
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $this->createServer(), [$slave1, $slave2]);
        $connectionPool->removeSlave($slave2);
        $this->assertEquals([$slave1], $connectionPool->getSlaves());
    }

    /**
     * Creates a driver for use in tests
     *
     * @return Mocks\Driver A driver
     */
    private function createDriver()
    {
        return new Mocks\Driver();
    }

    /**
     * Creates a server for use in tests
     *
     * @return Mocks\Server A server
     */
    private function createServer()
    {
        return new Mocks\Server();
    }
}