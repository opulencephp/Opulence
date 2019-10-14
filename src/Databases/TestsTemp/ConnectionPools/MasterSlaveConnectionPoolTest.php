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

use Opulence\Databases\ConnectionPools\MasterSlaveConnectionPool;
use Opulence\Databases\ConnectionPools\Strategies\ServerSelection\IServerSelectionStrategy;
use Opulence\Databases\TestsTemp\Mocks\Connection;
use Opulence\Databases\TestsTemp\Mocks\Driver;
use Opulence\Databases\TestsTemp\Mocks\Server;

/**
 * Tests the master/slave connection pool
 */
class MasterSlaveConnectionPoolTest extends \PHPUnit\Framework\TestCase
{
    public function testAddingSlave(): void
    {
        $slave = $this->createServer();
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $this->createServer());
        $connectionPool->addSlave($slave);
        $this->assertEquals([$slave], $connectionPool->getSlaves());
    }

    public function testAddingSlaves(): void
    {
        $slave1 = $this->createServer();
        $slave2 = $this->createServer();
        $slave3 = $this->createServer();
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $this->createServer(), [$slave1]);
        $connectionPool->addSlaves([$slave2, $slave3]);
        $this->assertEquals([$slave1, $slave2, $slave3], $connectionPool->getSlaves());
    }

    public function testCreatingConnectionWithNoSlaves(): void
    {
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $this->createServer());
        $this->assertEquals([], $connectionPool->getSlaves());
    }

    public function testGettingReadConnectionWithNoSlaves(): void
    {
        $master = $this->createServer();
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $master);
        $expectedConnection = new Connection($master);
        $this->assertEquals($expectedConnection, $connectionPool->getReadConnection());
    }

    public function testGettingReadConnectionWithPreferredServer(): void
    {
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $this->createServer());
        $preferredServer = new Server();
        $expectedConnection = new Connection($preferredServer);
        $this->assertEquals($expectedConnection, $connectionPool->getReadConnection($preferredServer));
    }

    public function testGettingReadConnectionWithSlaves(): void
    {
        $slave1 = $this->createServer();
        $slave2 = $this->createServer();
        $connectionPool = new MasterSlaveConnectionPool(
            $this->createDriver(),
            $this->createServer(),
            [$slave1, $slave2]
        );
        $expectedServers = [$slave1, $slave2];
        $expectedPdo = $connectionPool->getReadConnection();
        $slaveFound = false;

        foreach ($expectedServers as $server) {
            if ($expectedPdo->getServer() == $server) {
                $slaveFound = true;
            }
        }

        $this->assertTrue($slaveFound);
    }

    public function testGettingWriteConnectionWithNoSlaves(): void
    {
        $master = $this->createServer();
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $master);
        $expectedConnection = new Connection($master);
        $this->assertEquals($expectedConnection, $connectionPool->getWriteConnection());
    }

    public function testGettingWriteConnectionWithPreferredServer(): void
    {
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $this->createServer());
        $preferredServer = $this->createServer();
        $expectedConnection = new Connection($preferredServer);
        $this->assertEquals($expectedConnection, $connectionPool->getWriteConnection($preferredServer));
    }

    public function testRemovingSlave(): void
    {
        $slave1 = $this->createServer();
        $slave1->setDatabaseName('slave1');
        $slave2 = $this->createServer();
        $slave2->setDatabaseName('slave2');
        $connectionPool = new MasterSlaveConnectionPool(
            $this->createDriver(),
            $this->createServer(),
            [$slave1, $slave2]
        );
        $connectionPool->removeSlave($slave2);
        $this->assertEquals([$slave1], $connectionPool->getSlaves());
    }

    public function testSpecifyingSlaveServerSelectionStrategy(): void
    {
        $slave = $this->createServer();
        $strategy = $this->createMock(IServerSelectionStrategy::class);
        $strategy->expects($this->once())
            ->method('select')
            ->with([$slave])
            ->willReturn($slave);
        $connectionPool = new MasterSlaveConnectionPool(
            $this->createDriver(),
            $this->createServer(),
            [$slave],
            [],
            [],
            $strategy
        );
        $this->assertSame($slave, $connectionPool->getReadConnection()->getServer());
    }

    /**
     * Creates a driver for use in tests
     *
     * @return Driver A driver
     */
    private function createDriver(): Driver
    {
        return new Driver();
    }

    /**
     * Creates a server for use in tests
     *
     * @return Server A server
     */
    private function createServer(): Server
    {
        return new Server();
    }
}
