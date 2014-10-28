<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the single-server connection pool factory
 */
namespace RDev\Databases\SQL\Factories;
use RDev\Databases\SQL;
use RDev\Databases\SQL\Configs;

class SingleServerConnectionPoolFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var SingleServerConnectionPoolFactory The factory to use to create connection pools */
    private $factory = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->factory = new MasterSlaveConnectionPoolFactory();
    }

    /**
     * Tests getting the driver
     */
    public function testGettingDriver()
    {
        $configArray = [
            "driver" => "pdo_pgsql",
            "servers" => [
                "master" => new SQL\Server()
            ]
        ];
        $config = new Configs\MasterSlaveConnectionPoolConfig($configArray);
        $connectionPool = $this->factory->createFromConfig($config);
        $this->assertSame($config["driver"], $connectionPool->getDriver());
    }

    /**
     * Tests getting the master
     */
    public function testGettingMaster()
    {
        $master = new SQL\Server();
        $configArray = [
            "driver" => "pdo_pgsql",
            "servers" => [
                "master" => $master
            ]
        ];
        $config = new Configs\MasterSlaveConnectionPoolConfig($configArray);
        $connectionPool = $this->factory->createFromConfig($config);
        $this->assertSame($master, $connectionPool->getMaster());
    }
}