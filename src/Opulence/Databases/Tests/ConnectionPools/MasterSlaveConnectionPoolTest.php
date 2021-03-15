<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Databases\Tests\ConnectionPools;

use Opulence\Databases\ConnectionPools\MasterSlaveConnectionPool;
use Opulence\Databases\ConnectionPools\Strategies\ServerSelection\IServerSelectionStrategy;
use Opulence\Databases\Tests\Mocks\Connection;
use Opulence\Databases\Tests\Mocks\Driver;
use Opulence\Databases\Tests\Mocks\Server;

/**
 * Tests the master/slave connection pool
 */
class MasterSlaveConnectionPoolTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests adding a slave
     */
    public function testAddingSlave()
    {
        $slave = $this->createServer();
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $this->createServer());
        $connectionPool->addSlave($slave);
        $this->assertEquals([$slave], $connectionPool->getSlaves());
    }

    /**
     * Tests adding slaves
     */
    public function testAddingSlaves()
    {
        $slave1 = $this->createServer();
        $slave2 = $this->createServer();
        $slave3 = $this->createServer();
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $this->createServer(), [$slave1]);
        $connectionPool->addSlaves([$slave2, $slave3]);
        $this->assertEquals([$slave1, $slave2, $slave3], $connectionPool->getSlaves());
    }

    /**
     * Tests creating a connection with no slaves
     */
    public function testCreatingConnectionWithNoSlaves()
    {
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $this->createServer());
        $this->assertEquals([], $connectionPool->getSlaves());
    }

    /**
     * Tests getting the read connection with no slaves
     */
    public function testGettingReadConnectionWithNoSlaves()
    {
        $master = $this->createServer();
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $master);
        $expectedConnection = new Connection($master);
        $this->assertEquals($expectedConnection, $connectionPool->getReadConnection());
    }

    /**
     * Tests getting the read connection with a preferred server
     */
    public function testGettingReadConnectionWithPreferredServer()
    {
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $this->createServer());
        $preferredServer = new Server();
        $expectedConnection = new Connection($preferredServer);
        $this->assertEquals($expectedConnection, $connectionPool->getReadConnection($preferredServer));
    }

    /**
     * Tests getting the read connection with slaves
     */
    public function testGettingReadConnectionWithSlaves()
    {
        $slave1 = $this->createServer();
        $slave2 = $this->createServer();
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $this->createServer(),
            [$slave1, $slave2]);
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

    /**
     * Tests getting the write connection with no slaves
     */
    public function testGettingWriteConnectionWithNoSlaves()
    {
        $master = $this->createServer();
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $master);
        $expectedConnection = new Connection($master);
        $this->assertEquals($expectedConnection, $connectionPool->getWriteConnection());
    }

    /**
     * Tests getting the write connection with a preferred server
     */
    public function testGettingWriteConnectionWithPreferredServer()
    {
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $this->createServer());
        $preferredServer = $this->createServer();
        $expectedConnection = new Connection($preferredServer);
        $this->assertEquals($expectedConnection, $connectionPool->getWriteConnection($preferredServer));
    }

    /**
     * Tests removing a slave
     */
    public function testRemovingSlave()
    {
        $slave1 = $this->createServer();
        $slave1->setDatabaseName('slave1');
        $slave2 = $this->createServer();
        $slave2->setDatabaseName('slave2');
        $connectionPool = new MasterSlaveConnectionPool($this->createDriver(), $this->createServer(),
            [$slave1, $slave2]);
        $connectionPool->removeSlave($slave2);
        $this->assertEquals([$slave1], $connectionPool->getSlaves());
    }

    /**
     * Tests specifying a slave server selection strategy
     */
    public function testSpecifyingSlaveServerSelectionStrategy()
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
    private function createDriver()
    {
        return new Driver();
    }

    /**
     * Creates a server for use in tests
     *
     * @return Server A server
     */
    private function createServer()
    {
        return new Server();
    }
}
