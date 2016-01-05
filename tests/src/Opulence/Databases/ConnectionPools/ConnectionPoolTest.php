<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Databases\ConnectionPools;

use Opulence\Tests\Databases\ConnectionPools\Mocks\ConnectionPool;
use Opulence\Tests\Databases\Mocks\Driver;
use Opulence\Tests\Databases\Mocks\Server as MockServer;

/**
 * Tests the connection pool
 */
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