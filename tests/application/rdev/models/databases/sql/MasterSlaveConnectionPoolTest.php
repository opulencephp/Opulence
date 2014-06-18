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
        $factory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $slave1 = new Mocks\Server();
        $slave1->setDatabaseName("slave1");
        $slave2 = new Mocks\Server();
        $slave2->setDatabaseName("slave2");
        $connectionPool = new MasterSlaveConnectionPool($factory, $master, [$slave1]);
        $connectionPool->addSlave($slave2);
        $this->assertEquals([$slave1, $slave2], $connectionPool->getSlaves());
    }

    /**
     * Tests creating a connection with no slaves
     */
    public function testCreatingConnectionWithNoSlaves()
    {
        $factory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $connectionPool = new MasterSlaveConnectionPool($factory, $master);
        $this->assertEquals([], $connectionPool->getSlaves());
    }

    /**
     * Tests adding a single slave through the constructor
     */
    public function testCreatingConnectionWithSingleSlave()
    {
        $factory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $slave = new Mocks\Server();
        $connectionPool = new MasterSlaveConnectionPool($factory, $master, $slave);
        $this->assertEquals([$slave], $connectionPool->getSlaves());
    }

    /**
     * Tests creating a connection with slaves
     */
    public function testCreatingConnectionWithSlaves()
    {
        $factory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $slaves = [
            new Mocks\Server(),
            new Mocks\Server()
        ];
        $connectionPool = new MasterSlaveConnectionPool($factory, $master, $slaves);
        $this->assertEquals($slaves, $connectionPool->getSlaves());
    }

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
     * Tests getting the read connection with no slaves
     */
    public function testGettingReadConnectionWithNoSlaves()
    {
        $factory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $expectedConnection = new Mocks\Connection($master);
        $connectionPool = new MasterSlaveConnectionPool($factory, $master);
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
        $connectionPool = new MasterSlaveConnectionPool($factory, $master);
        $this->assertEquals($expectedConnection, $connectionPool->getReadConnection($preferredServer));
    }

    /**
     * Tests getting the read connection with slaves
     */
    public function testGettingReadConnectionWithSlaves()
    {
        $factory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $slave1 = new Mocks\Server();
        $slave2 = new Mocks\Server();
        $expectedServers = [$slave1, $slave2];
        $connectionPool = new MasterSlaveConnectionPool($factory, $master, [$slave1, $slave2]);
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
        $factory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $expectedConnection = new Mocks\Connection($master);
        $connectionPool = new MasterSlaveConnectionPool($factory, $master);
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
        $connectionPool = new MasterSlaveConnectionPool($factory, $master);
        $this->assertEquals($expectedConnection, $connectionPool->getWriteConnection($preferredServer));
    }

    /**
     * Tests initializing the pool with a slave server
     */
    public function testInitializingFromConfigWithASlaveServer()
    {
        $factory = $this->getConnectionFactory();
        $connectionPool = new MasterSlaveConnectionPool($factory);
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
        $factory = $this->getConnectionFactory();
        $connectionPool = new MasterSlaveConnectionPool($factory);
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
        $factory = $this->getConnectionFactory();
        $master = new Mocks\Server();
        $slave1 = new Mocks\Server();
        $slave1->setDatabaseName("slave1");
        $slave2 = new Mocks\Server();
        $slave2->setDatabaseName("slave2");
        $connectionPool = new MasterSlaveConnectionPool($factory, $master, [$slave1, $slave2]);
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