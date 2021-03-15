<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Databases\Tests\ConnectionPools;

use Opulence\Databases\Tests\ConnectionPools\Mocks\ConnectionPool;
use Opulence\Databases\Tests\Mocks\Driver;
use Opulence\Databases\Tests\Mocks\Server as MockServer;

/**
 * Tests the connection pool
 */
class ConnectionPoolTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests getting the list of driver names
     */
    public function testGettingDriverNames()
    {
        $this->assertEquals(['pdo_mysql', 'pdo_pgsql'], ConnectionPool::getDriverNames());
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
