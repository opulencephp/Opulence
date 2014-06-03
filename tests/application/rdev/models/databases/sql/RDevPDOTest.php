<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the RDevPDO class
 */
namespace RDev\Models\Databases\SQL;
use TBA\Models\Databases\SQL\PostgreSQL\Servers;

class RDevPDOTest extends \PHPUnit_Framework_TestCase
{
    /** @var Server A database server to connect to */
    private $server = null;
    /** @var RDevPDO The RDevPDO object we're connecting to */
    private $rDevPDO = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->server = new Servers\RDS();
        $this->rDevPDO = new RDevPDO($this->server);
    }

    /**
     * Does some housekeeping before ending the tests
     */
    public function tearDown()
    {
        $this->rDevPDO = null;
    }

    /**
     * Tests getting the server
     */
    public function testGetServer()
    {
        $this->assertEquals($this->server, $this->rDevPDO->getServer());
    }
}
 