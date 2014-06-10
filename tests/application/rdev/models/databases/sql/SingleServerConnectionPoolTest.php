<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the single server connection pool
 */
namespace RDev\Models\Databases\SQL;
use RDev\Models\Databases\SQL\PDO;

class SingleServerConnectionPoolTest extends \PHPUnit_Framework_TestCase
{
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
     * Tests getting the read connection without a preferred server
     */
    public function testGettingReadConnection()
    {
        $connectionFactory = new PDO\RDevPDOConnectionFactory();
        $master = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
        $expectedConnection = new PDO\RDevPDO($master);
        $connectionPool = new SingleServerConnectionPool($connectionFactory, $master);
        $this->assertEquals($expectedConnection, $connectionPool->getReadConnection());
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
        $connectionPool = new SingleServerConnectionPool($connectionFactory, $master);
        $this->assertEquals($expectedConnection, $connectionPool->getReadConnection($preferredServer));
    }

    /**
     * Tests getting the write connection without a preferred server
     */
    public function testGettingWriteConnection()
    {
        $connectionFactory = new PDO\RDevPDOConnectionFactory();
        $master = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
        $expectedConnection = new PDO\RDevPDO($master);
        $connectionPool = new SingleServerConnectionPool($connectionFactory, $master);
        $this->assertEquals($expectedConnection, $connectionPool->getWriteConnection());
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
        $connectionPool = new SingleServerConnectionPool($connectionFactory, $master);
        $this->assertEquals($expectedConnection, $connectionPool->getWriteConnection($preferredServer));
    }
} 