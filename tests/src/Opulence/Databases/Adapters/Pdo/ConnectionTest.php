<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Databases\Adapters\Pdo;

use Opulence\Databases\Providers\Provider;
use Opulence\Tests\Databases\Mocks\Server;

/**
 * Tests the Connection class
 */
class ConnectionTest extends \PHPUnit\Framework\TestCase
{
    /** @var Provider They provider this connection uses */
    private $provider = null;
    /** @var Server A database server to connect to */
    private $server = null;
    /** @var Connection The Connection object we're connecting to */
    private $pdo = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->provider = new Provider();
        $this->server = new Server();
        $this->pdo = new Connection($this->provider, $this->server, 'fakedsn', []);
    }

    /**
     * Does some housekeeping before ending the tests
     */
    public function tearDown()
    {
        $this->pdo = null;
    }

    /**
     * Tests getting the database provider
     */
    public function testGettingDatabaseProvider()
    {
        $this->assertEquals($this->provider, $this->pdo->getDatabaseProvider());
    }

    /**
     * Tests getting the server
     */
    public function testGettingServer()
    {
        $this->assertEquals($this->server, $this->pdo->getServer());
    }
}
