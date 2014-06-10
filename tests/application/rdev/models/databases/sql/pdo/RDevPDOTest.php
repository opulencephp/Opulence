<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the RDevPDO class
 */
namespace RDev\Models\Databases\SQL\PDO;
use RDev\Models\Databases\SQL;

class RDevPDOTest extends \PHPUnit_Framework_TestCase
{
    /** @var SQL\Server A database server to connect to */
    private $server = null;
    /** @var RDevPDO The RDevPDO object we're connecting to */
    private $pdo = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->server = $this->getMockForAbstractClass("RDev\\Models\\Databases\\SQL\\Server");
        $this->pdo = new RDevPDO($this->server);
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
 