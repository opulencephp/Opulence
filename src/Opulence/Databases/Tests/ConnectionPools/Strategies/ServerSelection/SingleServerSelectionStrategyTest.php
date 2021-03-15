<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Databases\Tests\ConnectionPools\Strategies\ServerSelection;

use InvalidArgumentException;
use Opulence\Databases\ConnectionPools\Strategies\ServerSelection\SingleServerSelectionStrategy;
use Opulence\Databases\Server;

/**
 * Tests the single server selection strategy
 */
class SingleServerSelectionStrategyTest extends \PHPUnit\Framework\TestCase
{
    /** @var SingleServerSelectionStrategy The strategy to use in tests */
    private $strategy = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->strategy = new SingleServerSelectionStrategy();
    }

    /**
     * Tests that an exception is thrown when passing an empty list of servers
     */
    public function testExceptionThrownWithEmptyListOfServers()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->strategy->select([]);
    }

    /**
     * Tests that selecting from a list of a servers always returns first
     */
    public function testSelectingFromListOfServersAlwaysReturnsFirst()
    {
        $server1 = $this->getServerMock();
        $server2 = $this->getServerMock();

        // Just test this a few times to guarantee that we're always selecting the first time
        for ($i = 0;$i < 5;$i++) {
            $this->assertSame($server1, $this->strategy->select([$server1, $server2]));
        }
    }

    /**
     * Tests selecting from a list of a single server
     */
    public function testSelectingFromListOfSingleServer()
    {
        $server = $this->getServerMock();
        $this->assertSame($server, $this->strategy->select([$server]));
    }

    /**
     * Tests selecting from a single server
     */
    public function testSelectingFromSingleServer()
    {
        $server = $this->getServerMock();
        $this->assertSame($server, $this->strategy->select($server));
    }

    /**
     * Gets a mock server
     *
     * @return Server|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getServerMock()
    {
        return $this->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
