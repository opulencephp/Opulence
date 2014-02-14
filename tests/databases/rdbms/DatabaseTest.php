<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests our database connection
 */
namespace RamODev\Databases\RDBMS;
use RamODev\Databases\RDBMS\PostgreSQL\Servers;

require_once(__DIR__ . "/../../../databases/rdbms/Database.php");
require_once(__DIR__ . "/../../../databases/rdbms/postgresql/servers/RDS.php");

class DatabaseTest extends \PHPUnit_Framework_TestCase
{
    /** @var Server A database server to connect to */
    private $server = null;
    /** @var Database The database we're connecting to */
    private $database = null;

    /**
     * Sets up our tests
     */
    public function setUp()
    {
        $this->server = new Servers\RDS();
        $this->database = new Database($this->server);
    }

    /**
     * Does some housekeeping before ending the tests
     */
    public function tearDown()
    {
        $this->database->close();
    }

    /**
     * Tests querying with a non-existent table
     */
    public function testBadSelect()
    {
        $this->database->connect();
        $results = $this->database->query("SELECT id FROM table_that_doesnt_exist WHERE id = :id", array("id" => 1));
        $this->assertFalse($results->hasResults());
    }

    /**
     * Tests closing an unopened connection
     */
    public function testClosingUnopenedConnection()
    {
        $this->database->close();
        $this->assertFalse($this->database->isConnected());
    }

    /**
     * Tests committing a transaction
     */
    public function testCommittingTransaction()
    {
        $this->database->connect();
        $this->database->startTransaction();
        $countBefore = $this->database->query("SELECT COUNT(*) FROM test")->getResult(0);
        $this->database->query("INSERT INTO test (name) VALUES (:name)", array("name" => "TEST"));
        $this->database->commitTransaction();
        $this->assertEquals($this->database->query("SELECT COUNT(*) FROM test")->getResult(0), $countBefore + 1);
    }

    /**
     * Tests connecting to a server
     */
    public function testConnecting()
    {
        $this->database->connect();
        $this->assertTrue($this->database->isConnected());
    }

    /**
     * Tests disconnecting from a server
     */
    public function testDisconnecting()
    {
        $this->database->connect();
        $this->database->close();
        $this->assertFalse($this->database->isConnected());
    }

    /**
     * Tests sending an empty parameter array
     */
    public function testEmptyParameterQuery()
    {
        $this->database->connect();
        $results = $this->database->query("SELECT COUNT(*) FROM test");
        $this->assertTrue($results->hasResults());
    }

    /**
     * Tests getting the server
     */
    public function testGetServer()
    {
        $this->assertEquals($this->server, $this->database->getServer());
    }

    /**
     * Tests getting the last insert ID
     */
    public function testGettingLastInsertID()
    {
        $this->database->connect();
        $this->database->startTransaction();
        $prevID = $this->database->query("SELECT MAX(id) FROM test")->getResult(0);
        $this->database->query("INSERT INTO test (name) VALUES (:name)", array("name" => "TEST"));
        $lastInsertID = $this->database->getLastInsertID("test_id_seq");
        $this->database->commitTransaction();
        $this->assertEquals($prevID + 1, $lastInsertID);
    }

    /**
     * Tests running a valid select command
     */
    public function testGoodSelect()
    {
        $this->database->connect();
        $results = $this->database->query("SELECT name FROM test WHERE id = :id", array("id" => 1));
        $this->assertEquals("Dave", $results->getResult(0, "name"));
    }

    /**
     * Tests checking to see if we're in a transaction
     */
    public function testIsInTransaction()
    {
        $this->database->connect();
        $this->database->startTransaction();
        $this->assertTrue($this->database->isInTransaction());
        $this->database->commitTransaction();
    }

    /**
     * Tests rolling back a transaction
     */
    public function testRollingBackTransaction()
    {
        $this->database->connect();
        $this->database->startTransaction();
        $countBefore = $this->database->query("SELECT COUNT(*) FROM test")->getResult(0);
        $this->database->query("INSERT INTO test (name) VALUES (:name)", array("name" => "TEST"));
        $this->database->rollBackTransaction();
        $this->assertEquals($this->database->query("SELECT COUNT(*) FROM test")->getResult(0), $countBefore);
    }
}
 