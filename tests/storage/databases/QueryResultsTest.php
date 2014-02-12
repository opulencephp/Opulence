<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 *
 *
 * Tests our query results
 */
namespace RamODev\Storage\Databases;
use RamODev\Storage\Databases\PostgreSQL\Servers;

require_once(__DIR__ . "/../../../storage/databases/Connection.php");
require_once(__DIR__ . "/../../../storage/databases/postgresql/servers/RDS.php");

class QueryResultsTest extends \PHPUnit_Framework_TestCase
{
    /** @var Server A database server to connect to */
    private $server = null;
    /** @var Connection The server connection to use */
    private $connection = null;

    /**
     * Sets up our tests
     */
    public function setUp()
    {
        $this->server = new Servers\RDS();
        $this->connection = new Connection($this->server);
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
 