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
        $config = [
            "driver" => new Mocks\Driver(),
            "servers" => [
                "master" => new Mocks\Server()
            ]
        ];
        $connectionPool = new MasterSlaveConnectionPool($config);
        $slave = new Mocks\Server();
        $connectionPool->addSlave($slave);
        $this->assertEquals([$slave], $connectionPool->getSlaves());
    }

    /**
     * Tests adding slaves
     */
    public function testAddingSlaves()
    {
        $config = [
            "driver" => new Mocks\Driver(),
            "servers" => [
                "master" => new Mocks\Server()
            ]
        ];
        $connectionPool = new MasterSlaveConnectionPool($config);
        $slave1 = new Mocks\Server();
        $slave2 = new Mocks\Server();
        $connectionPool->addSlaves([$slave1, $slave2]);
        $this->assertEquals([$slave1, $slave2], $connectionPool->getSlaves());
    }

    /**
     * Tests creating a connection with no slaves
     */
    public function testCreatingConnectionWithNoSlaves()
    {
        $config = [
            "driver" => new Mocks\Driver(),
            "servers" => [
                "master" => new Mocks\Server()
            ]
        ];
        $connectionPool = new MasterSlaveConnectionPool($config);
        $this->assertEquals([], $connectionPool->getSlaves());
    }

    /**
     * Tests getting the read connection with no slaves
     */
    public function testGettingReadConnectionWithNoSlaves()
    {
        $master = new Mocks\Server();
        $config = [
            "driver" => new Mocks\Driver(),
            "servers" => [
                "master" => $master
            ]
        ];
        $connectionPool = new MasterSlaveConnectionPool($config);
        $expectedConnection = new Mocks\Connection($master);
        $this->assertEquals($expectedConnection, $connectionPool->getReadConnection());
    }

    /**
     * Tests getting the read connection with a preferred server
     */
    public function testGettingReadConnectionWithPreferredServer()
    {
        $config = [
            "driver" => new Mocks\Driver(),
            "servers" => [
                "master" => new Mocks\Server()
            ]
        ];
        $connectionPool = new MasterSlaveConnectionPool($config);
        $preferredServer = new Mocks\Server();
        $expectedConnection = new Mocks\Connection($preferredServer);
        $this->assertEquals($expectedConnection, $connectionPool->getReadConnection($preferredServer));
    }

    /**
     * Tests getting the read connection with slaves
     */
    public function testGettingReadConnectionWithSlaves()
    {
        $slave1 = new Mocks\Server();
        $slave2 = new Mocks\Server();
        $config = [
            "driver" => new Mocks\Driver(),
            "servers" => [
                "master" => new Mocks\Server(),
                "slaves" => [$slave1, $slave2]
            ]
        ];
        $connectionPool = new MasterSlaveConnectionPool($config);
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
        $master = new Mocks\Server();
        $config = [
            "driver" => new Mocks\Driver(),
            "servers" => [
                "master" => $master
            ]
        ];
        $connectionPool = new MasterSlaveConnectionPool($config);
        $expectedConnection = new Mocks\Connection($master);
        $this->assertEquals($expectedConnection, $connectionPool->getWriteConnection());
    }

    /**
     * Tests getting the write connection with a preferred server
     */
    public function testGettingWriteConnectionWithPreferredServer()
    {
        $config = [
            "driver" => new Mocks\Driver(),
            "servers" => [
                "master" => new Mocks\Server()
            ]
        ];
        $connectionPool = new MasterSlaveConnectionPool($config);
        $preferredServer = new Mocks\Server();
        $expectedConnection = new Mocks\Connection($preferredServer);
        $this->assertEquals($expectedConnection, $connectionPool->getWriteConnection($preferredServer));
    }

    /**
     * Tests removing a slave
     */
    public function testRemovingSlave()
    {
        $slave1 = new Mocks\Server();
        $slave1->setDatabaseName("slave1");
        $slave2 = new Mocks\Server();
        $slave2->setDatabaseName("slave2");
        $config = [
            "driver" => new Mocks\Driver(),
            "servers" => [
                "master" => new Mocks\Server(),
                "slaves" => [$slave1, $slave2]
            ]
        ];
        $connectionPool = new MasterSlaveConnectionPool($config);
        $connectionPool->removeSlave($slave2);
        $this->assertEquals([$slave1], $connectionPool->getSlaves());
    }

    /**
     * Tests initializing the pool with a mix of already-instantiated server and configs
     */
    public function testWithAServerObject()
    {
        $config = [
            "driver" => new Mocks\Driver(),
            "servers" => [
                "master" => new Mocks\Server(),
                "slaves" => [
                    [
                        "host" => "8.8.8.8",
                        "username" => "foo",
                        "password" => "bar",
                        "databaseName" => "mydb"
                    ]
                ]
            ]
        ];
        $connectionPool = new MasterSlaveConnectionPool($config);
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\Server", $connectionPool->getMaster());

        /** @var Server $slave */
        foreach($connectionPool->getSlaves() as $slave)
        {
            $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\Server", $slave);
        }
    }

    /**
     * Tests initializing the pool with a slave server
     */
    public function testWithASlaveServer()
    {
        $config = [
            "driver" => new Mocks\Driver(),
            "servers" => [
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
            ]
        ];
        $connectionPool = new MasterSlaveConnectionPool($config);
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
}