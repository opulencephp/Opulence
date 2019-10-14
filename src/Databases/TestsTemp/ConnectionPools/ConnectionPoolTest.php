<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases\TestsTemp\ConnectionPools;

use Opulence\Databases\TestsTemp\ConnectionPools\Mocks\ConnectionPool;
use Opulence\Databases\TestsTemp\Mocks\Driver;
use Opulence\Databases\TestsTemp\Mocks\Server as MockServer;

/**
 * Tests the connection pool
 */
class ConnectionPoolTest extends \PHPUnit\Framework\TestCase
{
    public function testGettingDriverNames(): void
    {
        $this->assertEquals(['pdo_mysql', 'pdo_pgsql'], ConnectionPool::getDriverNames());
    }

    public function testSettingMaster(): void
    {
        $connectionPool = new ConnectionPool(new Driver(), new MockServer());
        $master = new MockServer();
        $connectionPool->setMaster($master);
        $this->assertSame($master, $connectionPool->getMaster());
    }
}
