<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the insert query
 */
namespace RDev\Models\Databases\SQL\PostgreSQL\QueryBuilders;

class InsertQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding a column to return
     */
    public function testAddReturning()
    {
        $query = new InsertQuery("users", ["name" => "dave"]);
        $query->returning("id")
            ->addReturning("name");
        $this->assertEquals("INSERT INTO users (name) VALUES (?) RETURNING id, name", $query->getSQL());
        $this->assertEquals([
            ["dave", \PDO::PARAM_STR]
        ], $query->getParameters());
    }

    /**
     * Tests all the methods in a single, complicated query
     */
    public function testEverything()
    {
        $query = new InsertQuery("users", ["name" => "dave"]);
        $query->addColumnValues(["email" => "foo@bar.com"])
            ->returning("id")
            ->addReturning("name");
        $this->assertEquals("INSERT INTO users (name, email) VALUES (?, ?) RETURNING id, name", $query->getSQL());
        $this->assertEquals([
            ["dave", \PDO::PARAM_STR],
            ["foo@bar.com", \PDO::PARAM_STR]
        ], $query->getParameters());
    }

    /**
     * Tests returning a column value
     */
    public function testReturning()
    {
        $query = new InsertQuery("users", ["name" => "dave"]);
        $query->returning("id", "name");
        $this->assertEquals("INSERT INTO users (name) VALUES (?) RETURNING id, name", $query->getSQL());
        $this->assertEquals([
            ["dave", \PDO::PARAM_STR]
        ], $query->getParameters());
    }
} 