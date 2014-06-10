<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the master/slave connection pool
 */
namespace RDev\Models\Databases\SQL;
use RDev\Models\Databases\SQL\PDO;

class MasterSlaveConnectionPoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding a slave
     */
    public function testAddingSlave()
    {
        $connectionFactory = new PDO\RDevPDOConnectionFactory();
        $master = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
        $slave1 = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
        $slave1->setDatabaseName("slave1");
        $slave2 = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
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
        $connectionFactory = new PDO\RDevPDOConnectionFactory();
        $master = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
        $connectionPool = new MasterSlaveConnectionPool($connectionFactory, $master);
        $this->assertEquals([], $connectionPool->getSlaves());
    }

    /**
     * Tests adding a single slave through the constructor
     */
    public function testCreatingConnectionWithSingleSlave()
    {
        $connectionFactory = new PDO\RDevPDOConnectionFactory();
        $master = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
        $slave = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
        $connectionPool = new MasterSlaveConnectionPool($connectionFactory, $master, $slave);
        $this->assertEquals([$slave], $connectionPool->getSlaves());
    }

    /**
     * Tests creating a connection with slaves
     */
    public function testCreatingConnectionWithSlaves()
    {
        $connectionFactory = new PDO\RDevPDOConnectionFactory();
        $master = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
        $slaves = [
            $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server"),
            $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server")
        ];
        $connectionPoolPool = new MasterSlaveConnectionPool($connectionFactory, $master, $slaves);
        $this->assertEquals($slaves, $connectionPoolPool->getSlaves());
    }

    /**
     * Tests getting the master after setting it in the constructor
     */
    public function testGettingMasterAfterSettingInConstructor()
    {
        $connectionFactory = new PDO\RDevPDOConnectionFactory();
        $master = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
        $connectionPoolPool = new MasterSlaveConnectionPool($connectionFactory, $master);
        $this->assertEquals($master, $connectionPoolPool->getMaster());
    }

    /**
     * Tests getting the read connection with no slaves
     */
    public function testGettingReadConnectionWithNoSlaves()
    {
        $connectionFactory = new PDO\RDevPDOConnectionFactory();
        $master = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
        $expectedConnection = new PDO\RDevPDO($master);
        $connectionPoolPool = new MasterSlaveConnectionPool($connectionFactory, $master);
        $this->assertEquals($expectedConnection, $connectionPoolPool->getReadConnection());
    }

    /**
     * Tests getting the read connection with a preferred server
     */
    public function testGettingReadConnectionWithPreferredServer()
    {
        $connectionFactory = new PDO\RDevPDOConnectionFactory();
        $master = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
        $preferredServer = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
        $expectedConnection = new PDO\RDevPDO($preferredServer);
        $connectionPool = new MasterSlaveConnectionPool($connectionFactory, $master);
        $this->assertEquals($expectedConnection, $connectionPool->getReadConnection($preferredServer));
    }

    /**
     * Tests getting the read connection with slaves
     */
    public function testGettingReadConnectionWithSlaves()
    {
        $connectionFactory = new PDO\RDevPDOConnectionFactory();
        $master = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
        $slave1 = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
        $slave2 = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
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
        $connectionFactory = new PDO\RDevPDOConnectionFactory();
        $master = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
        $expectedConnection = new PDO\RDevPDO($master);
        $connectionPoolPool = new MasterSlaveConnectionPool($connectionFactory, $master);
        $this->assertEquals($expectedConnection, $connectionPoolPool->getWriteConnection());
    }

    /**
     * Tests getting the write connection with a preferred server
     */
    public function testGettingWriteConnectionWithPreferredServer()
    {
        $connectionFactory = new PDO\RDevPDOConnectionFactory();
        $master = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
        $preferredServer = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
        $expectedConnection = new PDO\RDevPDO($preferredServer);
        $connectionPool = new MasterSlaveConnectionPool($connectionFactory, $master);
        $this->assertEquals($expectedConnection, $connectionPool->getWriteConnection($preferredServer));
    }

    /**
     * Tests removing a slave
     */
    public function testRemovingSlave()
    {
        $connectionFactory = new PDO\RDevPDOConnectionFactory();
        $master = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
        $slave1 = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
        $slave1->setDatabaseName("slave1");
        $slave2 = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
        $slave2->setDatabaseName("slave2");
        $connectionPoolPool = new MasterSlaveConnectionPool($connectionFactory, $master, [$slave1, $slave2]);
        $connectionPoolPool->removeSlave($slave2);
        $this->assertEquals([$slave1], $connectionPoolPool->getSlaves());
    }
} 