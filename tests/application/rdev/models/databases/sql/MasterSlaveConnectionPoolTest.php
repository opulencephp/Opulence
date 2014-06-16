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
        $connectionFactory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $slave1 = new Mocks\Server();
        $slave1->setDatabaseName("slave1");
        $slave2 = new Mocks\Server();
        $slave2->setDatabaseName("slave2");
        $connectionPool = new MasterSlaveConnectionPool($connectionFactory, $master, [$slave1]);
        $connectionPool->addSlave($slave2);
        $this->assertEquals([$slave1, $slave2], $connectionPool->getSlaves());
    }

    /**
     * Tests creating a connection with no slaves
     */
    public function testCreatingConnectionWithNoSlaves()
    {
        $connectionFactory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $connectionPool = new MasterSlaveConnectionPool($connectionFactory, $master);
        $this->assertEquals([], $connectionPool->getSlaves());
    }

    /**
     * Tests adding a single slave through the constructor
     */
    public function testCreatingConnectionWithSingleSlave()
    {
        $connectionFactory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $slave = new Mocks\Server();
        $connectionPool = new MasterSlaveConnectionPool($connectionFactory, $master, $slave);
        $this->assertEquals([$slave], $connectionPool->getSlaves());
    }

    /**
     * Tests creating a connection with slaves
     */
    public function testCreatingConnectionWithSlaves()
    {
        $connectionFactory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $slaves = [
            new Mocks\Server(),
            new Mocks\Server()
        ];
        $connectionPool = new MasterSlaveConnectionPool($connectionFactory, $master, $slaves);
        $this->assertEquals($slaves, $connectionPool->getSlaves());
    }

    /**
     * Tests getting the master after setting it in the constructor
     */
    public function testGettingMasterAfterSettingInConstructor()
    {
        $connectionFactory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $connectionPool = new MasterSlaveConnectionPool($connectionFactory, $master);
        $this->assertEquals($master, $connectionPool->getMaster());
    }

    /**
     * Tests getting the read connection with no slaves
     */
    public function testGettingReadConnectionWithNoSlaves()
    {
        $connectionFactory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $expectedConnection = new Mocks\Connection($master);
        $connectionPool = new MasterSlaveConnectionPool($connectionFactory, $master);
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
        $connectionPool = new MasterSlaveConnectionPool($connectionFactory, $master);
        $this->assertEquals($expectedConnection, $connectionPool->getReadConnection($preferredServer));
    }

    /**
     * Tests getting the read connection with slaves
     */
    public function testGettingReadConnectionWithSlaves()
    {
        $connectionFactory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $slave1 = new Mocks\Server();
        $slave2 = new Mocks\Server();
        $expectedServers = [$slave1, $slave2];
        $connectionPool = new MasterSlaveConnectionPool($connectionFactory, $master, [$slave1, $slave2]);
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
        $connectionFactory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $expectedConnection = new Mocks\Connection($master);
        $connectionPool = new MasterSlaveConnectionPool($connectionFactory, $master);
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
        $connectionPool = new MasterSlaveConnectionPool($connectionFactory, $master);
        $this->assertEquals($expectedConnection, $connectionPool->getWriteConnection($preferredServer));
    }

    /**
     * Tests removing a slave
     */
    public function testRemovingSlave()
    {
        $connectionFactory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $slave1 = new Mocks\Server();
        $slave1->setDatabaseName("slave1");
        $slave2 = new Mocks\Server();
        $slave2->setDatabaseName("slave2");
        $connectionPool = new MasterSlaveConnectionPool($connectionFactory, $master, [$slave1, $slave2]);
        $connectionPool->removeSlave($slave2);
        $this->assertEquals([$slave1], $connectionPool->getSlaves());
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