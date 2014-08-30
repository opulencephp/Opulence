<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the Connection class
 */
namespace RDev\Models\Databases\SQL\PDO;
use RDev\Models\Databases\SQL;
use RDev\Models\Databases\SQL\Providers;
use RDev\Tests\Models\Databases\SQL\Mocks;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var Providers\Provider They provider this connection uses */
    private $provider = null;
    /** @var SQL\Server A database server to connect to */
    private $server = null;
    /** @var Connection The Connection object we're connecting to */
    private $pdo = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->provider = new Providers\Provider();
        $this->server = new Mocks\Server();
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
        $this->assertEquals(new Providers\TypeMapper($this->provider), $this->pdo->getTypeMapper());
    }
}