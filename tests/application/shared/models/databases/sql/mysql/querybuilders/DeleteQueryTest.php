<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the delete query
 */
namespace RamODev\Application\Shared\Models\Databases\SQL\MySQL\QueryBuilders;

class DeleteQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests all the methods in a single, complicated query
     */
    public function testEverything()
    {
        $query = new DeleteQuery("users", "u");
        $query->where("u.id = :userId")
            ->andWhere("u.name = :name")
            ->orWhere("u.id = 10")
            ->addNamedPlaceholderValues(array("userId" => 18175))
            ->addNamedPlaceholderValue("name", "dave")
            ->limit(1);
        $this->assertEquals("DELETE FROM users AS u WHERE (u.id = :userId) AND (u.name = :name) OR (u.id = 10) LIMIT 1", $query->getSQL());
        $this->assertEquals(array("userId" => 18175, "name" => "dave"), $query->getParameters());
    }

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
     * Tests the limit clause with a named placeholder
     */
    public function testLimitWithNamedPlaceholder()
    {
        $query = new DeleteQuery("users");
        $query->limit(":limit");
        $this->assertEquals("DELETE FROM users LIMIT :limit", $query->getSQL());
    }
} 