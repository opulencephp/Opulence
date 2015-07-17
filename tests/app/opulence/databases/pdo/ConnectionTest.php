<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the Connection class
 */
namespace Opulence\Databases\PDO;
use Opulence\Databases;
use Opulence\Databases\Providers\Provider;
use Opulence\Databases\Providers\TypeMapper;
use Opulence\Tests\Databases\SQL\Mocks\Server;

class ConnectionTest extends \PHPUnit_Framework_TestCase
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
        $this->pdo = new Connection($this->provider, $this->server, "fakedsn", []);
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

    /**
     * Tests getting the type mapper
     */
    public function testGettingTypeMapper()
    {
        $this->assertEquals(new TypeMapper($this->provider), $this->pdo->getTypeMapper());
    }
}