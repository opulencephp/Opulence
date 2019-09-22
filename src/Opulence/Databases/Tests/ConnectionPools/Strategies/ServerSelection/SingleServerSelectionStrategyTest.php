<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases\Tests\ConnectionPools\Strategies\ServerSelection;

use InvalidArgumentException;
use Opulence\Databases\ConnectionPools\Strategies\ServerSelection\SingleServerSelectionStrategy;
use Opulence\Databases\Server;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the single server selection strategy
 */
class SingleServerSelectionStrategyTest extends \PHPUnit\Framework\TestCase
{
    private SingleServerSelectionStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new SingleServerSelectionStrategy();
    }

    public function testExceptionThrownWithEmptyListOfServers(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->strategy->select([]);
    }

    public function testSelectingFromListOfServersAlwaysReturnsFirst(): void
    {
        $server1 = $this->getServerMock();
        $server2 = $this->getServerMock();

        // Just test this a few times to guarantee that we're always selecting the first time
        for ($i = 0;$i < 5;$i++) {
            $this->assertSame($server1, $this->strategy->select([$server1, $server2]));
        }
    }

    public function testSelectingFromListOfSingleServer(): void
    {
        $server = $this->getServerMock();
        $this->assertSame($server, $this->strategy->select([$server]));
    }

    public function testSelectingFromSingleServer(): void
    {
        $server = $this->getServerMock();
        $this->assertSame($server, $this->strategy->select($server));
    }

    /**
     * Gets a mock server
     *
     * @return Server|MockObject
     */
    private function getServerMock()
    {
        return $this->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
