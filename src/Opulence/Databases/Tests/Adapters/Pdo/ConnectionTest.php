<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases\Tests\Adapters\Pdo;

use Opulence\Databases\Adapters\Pdo\Connection;
use Opulence\Databases\Providers\Provider;
use Opulence\Databases\Tests\Mocks\Server;

/**
 * Tests the Connection class
 */
class ConnectionTest extends \PHPUnit\Framework\TestCase
{
    /** @var Provider They provider this connection uses */
    private $provider;
    /** @var Server A database server to connect to */
    private $server;
    /** @var Connection The Connection object we're connecting to */
    private $pdo;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->provider = new Provider();
        $this->server = new Server();
        $this->pdo = new Connection($this->provider, $this->server, 'fakedsn', []);
    }

    /**
     * Does some housekeeping before ending the tests
     */
    protected function tearDown(): void
    {
        $this->pdo = null;
    }

    /**
     * Tests getting the database provider
     */
    public function testGettingDatabaseProvider(): void
    {
        $this->assertEquals($this->provider, $this->pdo->getDatabaseProvider());
    }

    /**
     * Tests getting the server
     */
    public function testGettingServer(): void
    {
        $this->assertEquals($this->server, $this->pdo->getServer());
    }
}
