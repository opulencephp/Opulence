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
        unset($this->sql);
    }

    /**
     * Tests querying with a non-existent table
     */
    public function testBadSelect()
    {
        $this->setExpectedException("RamODev\\Application\\Shared\\Models\\Databases\\SQL\\Exceptions\\SQLException");
        $this->sql->query("SELECT id FROM table_that_doesnt_exist WHERE id = :id", array("id" => 1));
    }

    /**
     * Tests committing a nested transaction
     */
    public function testCommittingNestedTransaction()
    {
        $this->sql->beginTransaction();
        $this->sql->query("SELECT COUNT(*) FROM test");
        $this->sql->query("INSERT INTO test (name) VALUES (:name)", array("name" => "TEST"));
        $this->sql->beginTransaction();
        $statement = $this->sql->query("SELECT COUNT(*) FROM test");
        $countBeforeSecondQuery = $statement->fetchAll(\PDO::FETCH_NUM)[0][0];
        $this->sql->query("INSERT INTO test (name) VALUES (:name)", array("name" => "TEST"));
        $this->sql->commit();
        $this->sql->commit();
        $statement = $this->sql->query("SELECT COUNT(*) FROM test");
        $this->assertEquals($statement->fetchAll(\PDO::FETCH_NUM)[0][0], $countBeforeSecondQuery + 1);
    }

    /**
     * Tests sending an empty parameter array
     */
    public function testEmptyParameterQuery()
    {
        $statement = $this->sql->query("SELECT COUNT(*) FROM test");
        $this->assertTrue($statement->rowCount() > 0);
    }

    /**
     * Tests getting the server
     */
    public function testGetServer()
    {
        $this->assertEquals($this->server, $this->sql->getServer());
    }

    /**
     * Tests running a valid select command
     */
    public function testGoodSelect()
    {
        $statement = $this->sql->query("SELECT name FROM test WHERE id = :id", array("id" => 1));
        $this->assertEquals("Dave", $statement->fetchAll(\PDO::FETCH_ASSOC)[0]["name"]);
    }

    /**
     * Tests rolling back a nested transaction
     */
    public function testRollingBackNestedTransaction()
    {
        $this->sql->beginTransaction();
        $statement = $this->sql->query("SELECT COUNT(*) FROM test");
        $countBeforeFirstQuery = $statement->fetchAll(\PDO::FETCH_NUM)[0][0];
        $this->sql->query("INSERT INTO test (name) VALUES (:name)", array("name" => "TEST"));
        $this->sql->beginTransaction();
        $this->sql->query("SELECT COUNT(*) FROM test");
        $this->sql->query("INSERT INTO test (name) VALUES (:name)", array("name" => "TEST"));
        $this->sql->rollBack();
        $statement = $this->sql->query("SELECT COUNT(*) FROM test");
        $this->assertEquals($statement->fetchAll(\PDO::FETCH_NUM)[0][0], $countBeforeFirstQuery);
    }
}
 