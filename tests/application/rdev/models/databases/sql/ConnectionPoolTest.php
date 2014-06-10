<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the connection pool
 */
namespace RDev\Models\Databases\SQL;
use RDev\Models\Databases\SQL\PDO;

class ConnectionPoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests setting the master
     */
    public function testSettingMaster()
    {
        $connectionFactory = $this->getMock("RDev\\Models\\Databases\\SQL\\IConnectionFactory");
        $master = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
        $connectionPool = $this->getMockForAbstractClass(
            "RDev\\Models\\Databases\\SQL\\ConnectionPool",
            [$connectionFactory]
        );
        $connectionPool->setMaster($master);
        $this->assertEquals($master, $connectionPool->getMaster());
    }
} 