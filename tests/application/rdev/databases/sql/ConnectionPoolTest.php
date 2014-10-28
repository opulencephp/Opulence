<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the connection pool
 */
namespace RDev\Databases\SQL;
use RDev\Databases\SQL\PDO\PostgreSQL;
use RDev\Tests\Databases\SQL\Mocks;

class ConnectionPoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the list of driver names
     */
    public function testGettingDriverNames()
    {
        $this->assertEquals(["pdo_mysql", "pdo_pgsql"], Mocks\ConnectionPool::getDriverNames());
    }

    /**
     * Tests setting the master
     */
    public function testSettingMaster()
    {
        $connectionPool = new Mocks\ConnectionPool(new Mocks\Driver(), new Mocks\Server());
        $master = new Mocks\Server();
        $connectionPool->setMaster($master);
        $this->assertSame($master, $connectionPool->getMaster());
    }
} 