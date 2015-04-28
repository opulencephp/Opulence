<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the connection pool
 */
namespace RDev\Databases;
use RDev\Tests\Databases\SQL\Mocks\ConnectionPool;
use RDev\Tests\Databases\SQL\Mocks\Driver;
use RDev\Tests\Databases\SQL\Mocks\Server as MockServer;

class ConnectionPoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the list of driver names
     */
    public function testGettingDriverNames()
    {
        $this->assertEquals(["pdo_mysql", "pdo_pgsql"], ConnectionPool::getDriverNames());
    }

    /**
     * Tests setting the master
     */
    public function testSettingMaster()
    {
        $connectionPool = new ConnectionPool(new Driver(), new MockServer());
        $master = new MockServer();
        $connectionPool->setMaster($master);
        $this->assertSame($master, $connectionPool->getMaster());
    }
} 