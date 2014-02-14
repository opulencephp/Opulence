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
 * Tests the update query
 */
namespace RamODev\Storage\RDBMS\QueryBuilders;

require_once(__DIR__ . "/../../../../storage/rdbms/postgresql/querybuilders/UpdateQuery.php");

class UpdateQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding more columns
     */
    public function testAddingMoreColumns()
    {
        $query = new UpdateQuery("users", "", array("name" => "david"));
        $query->addColumnValues(array("email" => "bar@foo.com"));
        $this->assertEquals("UPDATE users SET name = ?, email = ?", $query->getSQL());
        $this->assertEquals(array("david", "bar@foo.com"), $query->getParameters());
    }

    /**
     * Tests a basic query
     */
    public function testBasicQuery()
    {
        $query = new UpdateQuery("users", "", array("name" => "david"));
        $this->assertEquals("UPDATE users SET name = ?", $query->getSQL());
        $this->assertEquals(array("david"), $query->getParameters());
    }

    /**
     * Tests all our methods in a single, complicated query
     */
    public function testEverything()
    {
        $query = new UpdateQuery("users", "u", array("name" => "david"));
        $query->addColumnValues(array("email" => "bar@foo.com"))
            ->where("u.id = ?", "emails.userid = u.id", "emails.email = ?")
            ->orWhere("u.name = ?")
            ->andWhere("subscriptions.userid = u.id", "subscriptions.type = 'customer'")
            ->addUnnamedPlaceholderValues(array(18175, "foo@bar.com", "dave"));
        $this->assertEquals("UPDATE users AS u SET name = ?, email = ? WHERE (u.id = ?) AND (emails.userid = u.id) AND (emails.email = ?) OR (u.name = ?) AND (subscriptions.userid = u.id) AND (subscriptions.type = 'customer')", $query->getSQL());
        $this->assertEquals(array("david", "bar@foo.com", 18175, "foo@bar.com", "dave"), $query->getParameters());
    }

    /**
     * Tests adding a "WHERE" clause
     */
    public function testWhere()
    {
        $query = new UpdateQuery("users", "", array("name" => "david"));
        $query->where("id = ?")
            ->addUnnamedPlaceholderValue(18175);
        $this->assertEquals("UPDATE users SET name = ? WHERE (id = ?)", $query->getSQL());
        $this->assertEquals(array("david", 18175), $query->getParameters());
    }
} 