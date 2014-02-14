<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the insert query
 */
namespace RamODev\Databases\RDBMS\PostgreSQL\QueryBuilders;

require_once(__DIR__ . "/../../../../../databases/rdbms/postgresql/querybuilders/InsertQuery.php");

class InsertQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding a column to return
     */
    public function testAddReturning()
    {
        $query = new InsertQuery("users", array("name" => "dave"));
        $query->returning("id")
            ->addReturning("name");
        $this->assertEquals("INSERT INTO users (name) VALUES (?) RETURNING id, name", $query->getSQL());
        $this->assertEquals(array("dave"), $query->getParameters());
    }

    /**
     * Tests all our methods in a single, complicated query
     */
    public function testEverything()
    {
        $query = new InsertQuery("users", array("name" => "dave"));
        $query->addColumnValues(array("email" => "foo@bar.com"))
            ->returning("id")
            ->addReturning("name");
        $this->assertEquals("INSERT INTO users (name, email) VALUES (?, ?) RETURNING id, name", $query->getSQL());
        $this->assertEquals(array("dave", "foo@bar.com"), $query->getParameters());
    }

    /**
     * Tests returning a column value
     */
    public function testReturning()
    {
        $query = new InsertQuery("users", array("name" => "dave"));
        $query->returning("id", "name");
        $this->assertEquals("INSERT INTO users (name) VALUES (?) RETURNING id, name", $query->getSQL());
        $this->assertEquals(array("dave"), $query->getParameters());
    }
} 