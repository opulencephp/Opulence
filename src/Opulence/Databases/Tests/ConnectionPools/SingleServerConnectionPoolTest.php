<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases\Tests\ConnectionPools;

use Opulence\Databases\ConnectionPools\SingleServerConnectionPool;
use Opulence\Databases\Tests\Mocks\Connection;
use Opulence\Databases\Tests\Mocks\Driver;
use Opulence\Databases\Tests\Mocks\Server;

/**
 * Tests the single server connection pool
 */
class SingleServerConnectionPoolTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests getting the read connection without a preferred server
     */
    public function testGettingReadConnection(): void
    {
        $connectionPool = $this->getConnectionPool();
        $master = new Server();
        $expectedConnection = new Connection($master);
        $connectionPool->setMaster($master);
        $this->assertEquals($expectedConnection, $connectionPool->getReadConnection());
    }

    /**
     * Tests getting the read connection with a preferred server
     */
    public function testGettingReadConnectionWithPreferredServer(): void
    {
        $connectionPool = $this->getConnectionPool();
        $preferredServer = new Server();
        $expectedConnection = new Connection($preferredServer);
        $this->assertEquals($expectedConnection, $connectionPool->getReadConnection($preferredServer));
    }

    /**
     * Tests getting the write connection without a preferred server
     */
    public function testGettingWriteConnection(): void
    {
        $connectionPool = $this->getConnectionPool();
        $master = new Server();
        $expectedConnection = new Connection($master);
        $connectionPool->setMaster($master);
        $this->assertEquals($expectedConnection, $connectionPool->getWriteConnection());
    }

    /**
     * Tests getting the write connection with a preferred server
     */
    public function testGettingWriteConnectionWithPreferredServer(): void
    {
        $connectionPool = $this->getConnectionPool();
        $preferredServer = new Server();
        $expectedConnection = new Connection($preferredServer);
        $this->assertEquals($expectedConnection, $connectionPool->getWriteConnection($preferredServer));
    }

    /**
     * Gets a connection pool to use in the tests
     *
     * @return SingleServerConnectionPool The connection pool to use
     */
    private function getConnectionPool(): SingleServerConnectionPool
    {
        return new SingleServerConnectionPool(new Driver(), new Server());
    }
}
