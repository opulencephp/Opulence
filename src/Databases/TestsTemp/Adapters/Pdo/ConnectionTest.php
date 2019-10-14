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

use Opulence\Databases\Adapters\Pdo\Connection;
use Opulence\Databases\Providers\Provider;
use Opulence\Databases\TestsTemp\Mocks\Server;

/**
 * Tests the Connection class
 */
class ConnectionTest extends \PHPUnit\Framework\TestCase
{
    private Provider $provider;
    private Server $server;
    private ?Connection $pdo;

    protected function setUp(): void
    {
        $this->provider = new Provider();
        $this->server = new Server();
        $this->pdo = new Connection($this->provider, $this->server, 'fakedsn', []);
    }

    protected function tearDown(): void
    {
        $this->pdo = null;
    }

    public function testGettingDatabaseProvider(): void
    {
        $this->assertEquals($this->provider, $this->pdo->getDatabaseProvider());
    }

    public function testGettingServer(): void
    {
        $this->assertEquals($this->server, $this->pdo->getServer());
    }
}
