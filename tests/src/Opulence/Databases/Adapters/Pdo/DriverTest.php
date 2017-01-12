<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Databases\Adapters\Pdo;

use Opulence\Tests\Databases\Mocks\Connection;
use Opulence\Tests\Databases\Mocks\Driver;
use Opulence\Tests\Databases\Mocks\Server;

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
