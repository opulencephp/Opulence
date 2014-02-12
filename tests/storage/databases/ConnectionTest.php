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
 * Tests our server connection
 */
namespace RamODev\Storage\Databases;
use RamODev\Storage\Databases\PostgreSQL\Servers;

require_once(__DIR__ . "/../../../storage/databases/Connection.php");
require_once(__DIR__ . "/../../../storage/databases/postgresql/servers/RDS.php");

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var Server A database server to connect to */
    private $server = null;
    /** @var Connection The connection we're using */
    private $connection = null;

    /**
     * Sets up our tests
     */
    public function setUp()
    {
        $this->server = new Servers\RDS();
        $this->connection = new Connection($this->server);
    }

    /**
     * Does some housekeeping before ending the tests
     */
    public function tearDown()
    {
        $this->connection->close();
    }

    /**
     * Tests querying with a non-existent table
     */
    public function testBadSelect()
    {
        $this->connection->connect();
        $results = $this->connection->query("SELECT id FROM table_that_doesnt_exist WHERE id = :id", array("id" => 1));
        $this->assertFalse($results->hasResults());
    }

    /**
     * Tests closing an unopened connection
     */
    public function testClosingUnopenedConnection()
    {
        $this->connection->close();
        $this->assertFalse($this->connection->isConnected());
    }

    /**
     * Tests committing a transaction
     */
    public function testCommittingTransaction()
    {
        $this->connection->connect();
        $this->connection->startTransaction();
        $countBefore = $this->connection->query("SELECT COUNT(*) FROM test")->getResult(0);
        $this->connection->query("INSERT INTO test (name) VALUES (:name)", array("name" => "TEST"));
        $this->connection->commitTransaction();
        $this->assertEquals($this->connection->query("SELECT COUNT(*) FROM test")->getResult(0), $countBefore + 1);
    }

    /**
     * Tests connecting to a server
     */
    public function testConnecting()
    {
        $this->connection->connect();
        $this->assertTrue($this->connection->isConnected());
    }

    /**
     * Tests disconnecting from a server
     */
    public function testDisconnecting()
    {
        $this->connection->connect();
        $this->connection->close();
        $this->assertFalse($this->connection->isConnected());
    }

    /**
     * Tests sending an empty parameter array
     */
    public function testEmptyParameterQuery()
    {
        $this->connection->connect();
        $results = $this->connection->query("SELECT COUNT(*) FROM test");
        $this->assertTrue($results->hasResults());
    }

    /**
     * Tests getting the server
     */
    public function testGetServer()
    {
        $this->assertEquals($this->server, $this->connection->getServer());
    }

    /**
     * Tests getting the last insert ID
     */
    public function testGettingLastInsertID()
    {
        $this->connection->connect();
        $this->connection->startTransaction();
        $prevID = $this->connection->query("SELECT MAX(id) FROM test")->getResult(0);
        $this->connection->query("INSERT INTO test (name) VALUES (:name)", array("name" => "TEST"));
        $lastInsertID = $this->connection->getLastInsertID("test_id_seq");
        $this->connection->commitTransaction();
        $this->assertEquals($prevID + 1, $lastInsertID);
    }

    /**
     * Tests running a valid select command
     */
    public function testGoodSelect()
    {
        $this->connection->connect();
        $results = $this->connection->query("SELECT name FROM test WHERE id = :id", array("id" => 1));
        $this->assertEquals("Dave", $results->getResult(0, "name"));
    }

    /**
     * Tests checking to see if we're in a transaction
     */
    public function testIsInTransaction()
    {
        $this->connection->connect();
        $this->connection->startTransaction();
        $this->assertTrue($this->connection->isInTransaction());
        $this->connection->commitTransaction();
    }

    /**
     * Tests rolling back a transaction
     */
    public function testRollingBackTransaction()
    {
        $this->connection->connect();
        $this->connection->startTransaction();
        $countBefore = $this->connection->query("SELECT COUNT(*) FROM test")->getResult(0);
        $this->connection->query("INSERT INTO test (name) VALUES (:name)", array("name" => "TEST"));
        $this->connection->rollBackTransaction();
        $this->assertEquals($this->connection->query("SELECT COUNT(*) FROM test")->getResult(0), $countBefore);
    }
}
 