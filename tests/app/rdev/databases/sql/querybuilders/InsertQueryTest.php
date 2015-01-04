<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the insert query
 */
namespace RDev\Databases\SQL\QueryBuilders;

class InsertQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding more columns to the query
     */
    public function testAddingMoreColumns()
    {
        $query = new InsertQuery("users", ["name" => "dave"]);
        $query->addColumnValues(["email" => "foo@bar.com"]);
        $this->assertEquals("INSERT INTO users (name, email) VALUES (?, ?)", $query->getSQL());
        $this->assertEquals([
            ["dave", \PDO::PARAM_STR],
            ["foo@bar.com", \PDO::PARAM_STR]
        ], $query->getParameters());
    }

    /**
     * Tests a basic query
     */
    public function testBasicQuery()
    {
        $query = new InsertQuery("users", ["name" => "dave", "email" => "foo@bar.com"]);
        $this->assertEquals("INSERT INTO users (name, email) VALUES (?, ?)", $query->getSQL());
        $this->assertEquals([
            ["dave", \PDO::PARAM_STR],
            ["foo@bar.com", \PDO::PARAM_STR]
        ], $query->getParameters());
    }

    /**
     * Tests all the methods in a single, complicated query
     */
    public function testEverything()
    {
        $query = new InsertQuery("users", ["name" => "dave"]);
        $query->addColumnValues(["email" => "foo@bar.com"]);
        $this->assertEquals("INSERT INTO users (name, email) VALUES (?, ?)", $query->getSQL());
        $this->assertEquals([
            ["dave", \PDO::PARAM_STR],
            ["foo@bar.com", \PDO::PARAM_STR]
        ], $query->getParameters());
    }
} 