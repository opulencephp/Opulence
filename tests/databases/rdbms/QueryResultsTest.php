<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the query results
 */
namespace RamODev\Databases\RDBMS;
use RamODev\Databases\RDBMS\PostgreSQL\Servers;

require_once(__DIR__ . "/../../../databases/rdbms/Database.php");
require_once(__DIR__ . "/../../../databases/rdbms/postgresql/servers/RDS.php");

class QueryResultsTest extends \PHPUnit_Framework_TestCase
{
    /** @var Server A database server to connect to */
    private $server = null;
    /** @var Database The server connection to use */
    private $connection = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->server = new Servers\RDS();
        $this->connection = new Database($this->server);
        $this->connection->connect();
    }

    /**
     * Does some housekeeping before ending the tests
     */
    public function tearDown()
    {
        $this->connection->close();
    }

    /**
     * Tests checking that a query that should have results does have results
     */
    public function testCheckingForResults()
    {
        $results = $this->connection->query("SELECT name FROM test");
        $this->assertTrue($results->hasResults());
    }

    /**
     * Tests getting the number of rows in the results
     */
    public function testGettingNumResults()
    {
        $results = $this->connection->query("SELECT name FROM test");
        $this->assertGreaterThan(0, $results->getNumResults());
    }

    /**
     * Tests getting a row for a query that doesn't return data
     */
    public function testGettingRowForQueryThatDoesNotReturnData()
    {
        $results = $this->connection->query("SELECT id FROM table_that_doesnt_exist");
        $hasResults = false;

        while($row = $results->getRow())
        {
            $hasResults = true;
        }

        $this->assertFalse($hasResults);
    }

    /**
     * Tests getting a row for a query that does return data
     */
    public function testGettingRowForQueryThatDoesReturnData()
    {
        $results = $this->connection->query("SELECT id FROM test");
        $hasResults = false;

        while($row = $results->getRow())
        {
            $hasResults = true;
        }

        $this->assertTrue($hasResults);
    }

    /**
     * Tests getting a result with the column specified
     */
    public function testGettingRowWithColumnSpecified()
    {
        $results = $this->connection->query("SELECT name FROM test");
        $this->assertNotEmpty($results->getResult(0, "name"));
    }

    /**
     * Tests getting a result without the column specified
     */
    public function testGettingRowWithNoColumnSpecified()
    {
        $results = $this->connection->query("SELECT name FROM test");
        $this->assertNotEmpty($results->getResult(0));
    }

    /**
     * Tests getting the server connection
     */
    public function testGettingServerConnection()
    {
        $results = $this->connection->query("SELECT COUNT(*) FROM test");
        $this->assertEquals($this->connection, $results->getServerConnection());
    }
}
 