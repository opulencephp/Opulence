<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the insert query
 */
namespace RamODev\Databases\RDBMS\QueryBuilders;

require_once(__DIR__ . "/../../../../databases/rdbms/postgresql/querybuilders/InsertQuery.php");

class InsertQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding more columns to the query
     */
    public function testAddingMoreColumns()
    {
        $query = new InsertQuery("users", array("name" => "dave"));
        $query->addColumnValues(array("email" => "foo@bar.com"));
        $this->assertEquals("INSERT INTO users (name, email) VALUES (?, ?)", $query->getSQL());
        $this->assertEquals(array("dave", "foo@bar.com"), $query->getParameters());
    }

    /**
     * Tests a basic query
     */
    public function testBasicQuery()
    {
        $query = new InsertQuery("users", array("name" => "dave", "email" => "foo@bar.com"));
        $this->assertEquals("INSERT INTO users (name, email) VALUES (?, ?)", $query->getSQL());
        $this->assertEquals(array("dave", "foo@bar.com"), $query->getParameters());
    }

    /**
     * Tests all our methods in a single, complicated query
     */
    public function testEverything()
    {
        $query = new InsertQuery("users", array("name" => "dave"));
        $query->addColumnValues(array("email" => "foo@bar.com"));
        $this->assertEquals("INSERT INTO users (name, email) VALUES (?, ?)", $query->getSQL());
        $this->assertEquals(array("dave", "foo@bar.com"), $query->getParameters());
    }
} 