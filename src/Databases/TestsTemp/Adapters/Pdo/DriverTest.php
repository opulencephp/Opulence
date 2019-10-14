<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases\TestsTemp\Adapters\Pdo;

use Opulence\Databases\TestsTemp\Mocks\Connection;
use Opulence\Databases\TestsTemp\Mocks\Driver;
use Opulence\Databases\TestsTemp\Mocks\Server;

/**
 * Tests the PDO driver
 */
class DriverTest extends \PHPUnit\Framework\TestCase
{
    public function testConnectingToServer(): void
    {
        $server = new Server();
        $driver = new Driver();
        $this->assertInstanceOf(Connection::class, $driver->connect($server));
    }
}
