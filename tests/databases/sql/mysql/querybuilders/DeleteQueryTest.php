<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the delete query
 */
namespace RamODev\Databases\SQL\MySQL\QueryBuilders;

require_once(__DIR__ . "/../../../../../databases/sql/mysql/querybuilders/DeleteQuery.php");

class DeleteQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the limit clause
     */
    public function testLimit()
    {
        $query = new DeleteQuery("users");
        $query->limit(1);
        $this->assertEquals("DELETE FROM users LIMIT 1", $query->getSQL());
    }

    /**
     * Tests all the methods in a single, complicated query
     */
    public function testEverything()
    {
        $query = new DeleteQuery("users", "u");
        $query->where("u.id = :userID")
            ->andWhere("u.name = :name")
            ->orWhere("u.id = 10")
            ->addNamedPlaceholderValues(array("userID" => 18175))
            ->addNamedPlaceholderValue("name", "dave")
            ->limit(1);
        $this->assertEquals("DELETE FROM users AS u WHERE (u.id = :userID) AND (u.name = :name) OR (u.id = 10) LIMIT 1", $query->getSQL());
        $this->assertEquals(array("userID" => 18175, "name" => "dave"), $query->getParameters());
    }
} 