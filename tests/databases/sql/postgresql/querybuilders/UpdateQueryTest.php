<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the update query
 */
namespace RamODev\Databases\SQL\PostgreSQL\QueryBuilders;

require_once(__DIR__ . "/../../../../../databases/sql/postgresql/querybuilders/UpdateQuery.php");

class UpdateQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding to a "RETURNING" clause
     */
    public function testAddReturning()
    {
        $query = new UpdateQuery("users", "", array("name" => "david"));
        $query->returning("id")
            ->addReturning("name");
        $this->assertEquals("UPDATE users SET name = ? RETURNING id, name", $query->getSQL());
        $this->assertEquals(array("david"), $query->getParameters());
    }

    /**
     * Tests all the methods in a single, complicated query
     */
    public function testEverything()
    {
        $query = new UpdateQuery("users", "u", array("name" => "david"));
        $query->addColumnValues(array("email" => "bar@foo.com"))
            ->where("u.id = ?", "emails.userid = u.id", "emails.email = ?")
            ->orWhere("u.name = ?")
            ->andWhere("subscriptions.userid = u.id", "subscriptions.type = 'customer'")
            ->returning("u.id")
            ->addReturning("u.name")
            ->addUnnamedPlaceholderValues(array(18175, "foo@bar.com", "dave"));
        $this->assertEquals("UPDATE users AS u SET name = ?, email = ? WHERE (u.id = ?) AND (emails.userid = u.id) AND (emails.email = ?) OR (u.name = ?) AND (subscriptions.userid = u.id) AND (subscriptions.type = 'customer') RETURNING u.id, u.name", $query->getSQL());
        $this->assertEquals(array("david", "bar@foo.com", 18175, "foo@bar.com", "dave"), $query->getParameters());
    }

    /**
     * Tests adding a "RETURNING" clause
     */
    public function testReturning()
    {
        $query = new UpdateQuery("users", "", array("name" => "david"));
        $query->returning("id");
        $this->assertEquals("UPDATE users SET name = ? RETURNING id", $query->getSQL());
        $this->assertEquals(array("david"), $query->getParameters());
    }
} 