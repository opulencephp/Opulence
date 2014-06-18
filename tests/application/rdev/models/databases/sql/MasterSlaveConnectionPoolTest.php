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
        $connectionPool = $this->getConnectionPool();
        $slave = new Mocks\Server();
        $connectionPool->addSlave($slave);
        $this->assertEquals([$slave], $connectionPool->getSlaves());
    }

    /**
     * Tests adding slaves
     */
    public function testAddingSlaves()
    {
        $connectionPool = $this->getConnectionPool();
        $slave1 = new Mocks\Server();
        $slave1->setDatabaseName("slave1");
        $slave2 = new Mocks\Server();
        $slave2->setDatabaseName("slave2");
        $connectionPool->addSlaves([$slave1, $slave2]);
        $this->assertEquals([$slave1, $slave2], $connectionPool->getSlaves());
    }

    /**
     * Tests creating a connection with no slaves
     */
    public function testCreatingConnectionWithNoSlaves()
    {
        $connectionPool = $this->getConnectionPool();
        $this->assertEquals([], $connectionPool->getSlaves());
    }

    /**
     * Tests getting the read connection with no slaves
     */
    public function testGettingReadConnectionWithNoSlaves()
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
     * Tests getting the read connection with slaves
     */
    public function testGettingReadConnectionWithSlaves()
    {
        $connectionPool = $this->getConnectionPool();
        $slave1 = new Mocks\Server();
        $slave2 = new Mocks\Server();
        $expectedServers = [$slave1, $slave2];
        $connectionPool->addSlaves([$slave1, $slave2]);
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
     * Tests initializing the pool with a slave server
     */
    public function testInitializingFromConfigWithASlaveServer()
    {
        $connectionPool = $this->getConnectionPool();
        $config = [
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
        ];
        $connectionPool->initFromConfig($config);
        $this->assertEquals(1, count($connectionPool->getSlaves()));
        $slaveFound = false;

        /** @var Server $slave */
        foreach($connectionPool->getSlaves() as $slave)
        {
            if($slave->getHost() == "8.8.8.8")
            {
                $slaveFound = true;
            }
        }

        $this->assertTrue($slaveFound);
    }

    /**
     * Tests initializing the pool with a mix of already-instantiated server and configs
     */
    public function testInitializingFromConfigWithServerObject()
    {
        $connectionPool = $this->getConnectionPool();
        $config = [
            "master" => new Mocks\Server(),
            "slaves" => [
                [
                    "host" => "8.8.8.8",
                    "username" => "foo",
                    "password" => "bar",
                    "databaseName" => "mydb"
                ]
            ]
        ];
        $connectionPool->initFromConfig($config);
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\Server", $connectionPool->getMaster());

        /** @var Server $slave */
        foreach($connectionPool->getSlaves() as $slave)
        {
            $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\Server", $slave);
        }
    }

    /**
     * Tests removing a slave
     */
    public function testRemovingSlave()
    {
        $connectionPool = $this->getConnectionPool();
        $slave1 = new Mocks\Server();
        $slave1->setDatabaseName("slave1");
        $slave2 = new Mocks\Server();
        $slave2->setDatabaseName("slave2");
        $connectionPool->addSlaves([$slave1, $slave2]);
        $connectionPool->removeSlave($slave2);
        $this->assertEquals([$slave1], $connectionPool->getSlaves());
    }

    /**
     * Gets a connection pool to use in the tests
     *
     * @return MasterSlaveConnectionPool The connection pool to use
     */
    private function getConnectionPool()
    {
        $driver = new Mocks\Driver();
        $factory = new ConnectionFactory($driver);

        return new MasterSlaveConnectionPool($factory);
    }
} 