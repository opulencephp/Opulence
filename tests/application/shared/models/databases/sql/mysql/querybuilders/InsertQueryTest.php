<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the insert query
 */
namespace RDev\Application\Shared\Models\Databases\SQL\MySQL\QueryBuilders;

class InsertQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding columns to the update portion of an INSERT/UPDATE
     */
    public function testAddingColumnsToUpdate()
    {
        $query = new InsertQuery("users", array("name" => "dave", "email" => "foo@bar.com"));
        $query->update(array("name" => "dave"))
            ->addUpdateColumnValues(array("email" => "foo@bar.com"));
        $this->assertEquals("INSERT INTO users (name, email) VALUES (?, ?) ON DUPLICATE KEY UPDATE name = ?, email = ?", $query->getSQL());
        $this->assertEquals(array(
            array("dave", \PDO::PARAM_STR),
            array("foo@bar.com", \PDO::PARAM_STR)
        ), $query->getParameters());
    }

    /**
     * Tests a basic query
     */
    public function testBasicQuery()
    {
        $query = new InsertQuery("users", array("name" => "dave", "email" => "foo@bar.com"));
        $this->assertEquals("INSERT INTO users (name, email) VALUES (?, ?)", $query->getSQL());
        $this->assertEquals(array(
            array("dave", \PDO::PARAM_STR),
            array("foo@bar.com", \PDO::PARAM_STR)
        ), $query->getParameters());
    }

    /**
     * Tests all the methods in a single, complicated query
     */
    public function testEverything()
    {
        $query = new InsertQuery("users", array("name" => "dave", "email" => "foo@bar.com"));
        $query->update(array("name" => "dave"))
            ->addUpdateColumnValues(array("email" => "foo@bar.com"));
        $this->assertEquals("INSERT INTO users (name, email) VALUES (?, ?) ON DUPLICATE KEY UPDATE name = ?, email = ?", $query->getSQL());
        $this->assertEquals(array(
            array("dave", \PDO::PARAM_STR),
            array("foo@bar.com", \PDO::PARAM_STR)
        ), $query->getParameters());
    }

    /**
     * Tests the INSERT/UPDATE ability
     */
    public function testInsertUpdate()
    {
        $query = new InsertQuery("users", array("name" => "dave", "email" => "foo@bar.com"));
        $query->update(array("name" => "dave", "email" => "foo@bar.com"));
        $this->assertEquals("INSERT INTO users (name, email) VALUES (?, ?) ON DUPLICATE KEY UPDATE name = ?, email = ?", $query->getSQL());
        $this->assertEquals(array(
            array("dave", \PDO::PARAM_STR),
            array("foo@bar.com", \PDO::PARAM_STR)
        ), $query->getParameters());
    }
} 