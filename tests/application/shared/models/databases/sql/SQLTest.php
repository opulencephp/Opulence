<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the SQL class
 */
namespace RamODev\Application\Shared\Models\Databases\SQL;
use RamODev\Application\TBA\Models\Databases\SQL\PostgreSQL\Servers;

class DatabaseTest extends \PHPUnit_Framework_TestCase
{
    /** @var Server A database server to connect to */
    private $server = null;
    /** @var SQL The SQL object we're connecting to */
    private $sql = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->server = new Servers\RDS();
        $this->sql = new SQL($this->server);
    }

    /**
     * Does some housekeeping before ending the tests
     */
    public function tearDown()
    {
        $this->sql = null;
    }

    /**
     * Tests getting the server
     */
    public function testGetServer()
    {
        $this->assertEquals($this->server, $this->sql->getServer());
    }
}
 