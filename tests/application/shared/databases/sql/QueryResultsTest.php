<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the query results
 */
namespace RamODev\Application\Shared\Databases\SQL;
use RamODev\Application\TBA\Databases\SQL\PostgreSQL\Servers;

class QueryResultsTest extends \PHPUnit_Framework_TestCase
{
    /** @var Server A server to connect to */
    private $server = null;
    /** @var Database The database to use */
    private $database = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->server = new Servers\RDS();
        $this->database = new Database($this->server);
        $this->database->connect();
    }

    /**
     * Does some housekeeping before ending the tests
     */
    public function tearDown()
    {
        $this->database->close();
    }

    /**
     * Tests checking that a query that should have results does have results
     */
    public function testCheckingForResults()
    {
        $results = $this->database->query("SELECT name FROM test");
        $this->assertTrue($results->hasResults());
    }

    /**
     * Tests getting all the rows for a query that doesn't return data
     */
    public function testGettingAllRowsForQueryThatDoesNotReturnData()
    {
        $results = $this->database->query("SELECT id FROM test");
        $this->assertGreaterThan(0, count($results->getAllRows()));
    }

    /**
     * Tests getting all the rows for a query that does return data
     */
    public function testGettingAllRowsForQueryThatDoesReturnData()
    {
        $results = $this->database->query("SELECT id FROM table_that_doesnt_exist");
        $this->assertEquals(0, count($results->getAllRows()));
    }

    /**
     * Tests getting the number of rows in the results
     */
    public function testGettingNumResults()
    {
        $results = $this->database->query("SELECT name FROM test");
        $this->assertGreaterThan(0, $results->getNumResults());
    }

    /**
     * Tests getting a row for a query that doesn't return data
     */
    public function testGettingRowForQueryThatDoesNotReturnData()
    {
        $results = $this->database->query("SELECT id FROM table_that_doesnt_exist");
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
        $results = $this->database->query("SELECT id FROM test");
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
        $results = $this->database->query("SELECT name FROM test");
        $this->assertNotEmpty($results->getResult(0, "name"));
    }

    /**
     * Tests getting a result without the column specified
     */
    public function testGettingRowWithNoColumnSpecified()
    {
        $results = $this->database->query("SELECT name FROM test");
        $this->assertNotEmpty($results->getResult(0));
    }
}
 