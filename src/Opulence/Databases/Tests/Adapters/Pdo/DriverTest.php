<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Databases\Tests\Adapters\Pdo;

use Opulence\Databases\Tests\Mocks\Connection;
use Opulence\Databases\Tests\Mocks\Driver;
use Opulence\Databases\Tests\Mocks\Server;

/**
 * Tests the PDO driver
 */
class DriverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests connecting to a server
     */
    public function testConnectingToServer()
    {
        $server = new Server();
        $driver = new Driver();
        $this->assertInstanceOf(Connection::class, $driver->connect($server));
    }
}
