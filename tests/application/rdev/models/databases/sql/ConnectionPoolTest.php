<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the connection pool
 */
namespace RDev\Models\Databases\SQL;
use RDev\Models\Databases\SQL\PDO\PostgreSQL;
use RDev\Tests\Models\Databases\SQL\Mocks;

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
        $config = [
            "driver" => new Mocks\Driver(),
            "servers" => [
                "master" => new Mocks\Server()
            ]
        ];
        $connectionPool = new Mocks\ConnectionPool($config);
        $master = new Mocks\Server();
        $connectionPool->setMaster($master);
        $this->assertEquals($master, $connectionPool->getMaster());
    }

    /**
     * Tests using the MySQL PDO driver
     */
    public function testUsingMySQLPDO()
    {
        $config = [
            "driver" => "pdo_mysql",
            "servers" => [
                "master" => new Mocks\Server()
            ]
        ];
        $connectionPool = new Mocks\ConnectionPool($config);
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\PDO\\MySQL\\Driver", $connectionPool->getDriver());
    }

    /**
     * Tests using the PostgreSQL PDO driver
     */
    public function testUsingPostgreSQLPDO()
    {
        $config = [
            "driver" => "pdo_pgsql",
            "servers" => [
                "master" => new Mocks\Server()
            ]
        ];
        $connectionPool = new Mocks\ConnectionPool($config);
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\PDO\\PostgreSQL\\Driver", $connectionPool->getDriver());
    }
} 