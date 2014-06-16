<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the Connection class
 */
namespace RDev\Models\Databases\SQL\PDO;
use RDev\Models\Databases\SQL;
use RDev\Tests\Models\Databases\SQL\Mocks;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var SQL\Server A database server to connect to */
    private $server = null;
    /** @var Connection The Connection object we're connecting to */
    private $pdo = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->server = new Mocks\Server();
        $this->pdo = new Connection($this->server, "fakedsn", []);
    }

    /**
     * Does some housekeeping before ending the tests
     */
    public function tearDown()
    {
        $this->pdo = null;
    }

    /**
     * Tests getting the server
     */
    public function testGetServer()
    {
        $this->assertEquals($this->server, $this->pdo->getServer());
    }
}
 