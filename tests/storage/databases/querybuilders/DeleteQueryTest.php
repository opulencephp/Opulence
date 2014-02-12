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
 * Tests the delete query
 */
namespace Storage\Databases\QueryBuilders;

require_once(__DIR__ . "/../../../../storage/databases/querybuilders/DeleteQuery.php");

class DeleteQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding a "USING" expression
     */
    public function testAddingUsing()
    {
        $query = new DeleteQuery("users");
        $query->using("emails")
            ->addUsing("subscriptions")
            ->where("users.id = emails.userid AND emails.email = 'foo@bar.com'")
            ->orWhere("subscriptions.userid = users.id AND subscriptions.type = 'customer'");
        $this->assertEquals("DELETE FROM users USING emails, subscriptions WHERE (users.id = emails.userid AND emails.email = 'foo@bar.com') OR (subscriptions.userid = users.id AND subscriptions.type = 'customer')", $query->getSQL());
    }

    /**
     * Tests adding an "AND" where condition
     */
    public function testAndWhere()
    {
        $query = new DeleteQuery("users");
        $query->where("id = 1")
            ->andWhere("name = 'dave'");
        $this->assertEquals("DELETE FROM users WHERE (id = 1) AND (name = 'dave')", $query->getSQL());
    }

    /**
     * Tests the most basic query we can run
     */
    public function testBasicQuery()
    {
        $query = new DeleteQuery("users");
        $this->assertEquals("DELETE FROM users", $query->getSQL());
    }

    /**
     * Tests all our methods in a single, complicated query
     */
    public function testEverything()
    {
        $query = new DeleteQuery("users", "u");
        $query->using("emails")
            ->addUsing("subscriptions")
            ->where("u.id = :userID", "emails.userid = u.id", "emails.email = :email")
            ->orWhere("u.name = :name")
            ->andWhere("subscriptions.userid = u.id", "subscriptions.type = 'customer'")
            ->addNamedPlaceholderValues(array("userID" => 18175, "email" => "foo@bar.com", "name" => "dave"));
        $this->assertEquals("DELETE FROM users AS u USING emails, subscriptions WHERE (u.id = :userID) AND (emails.userid = u.id) AND (emails.email = :email) OR (u.name = :name) AND (subscriptions.userid = u.id) AND (subscriptions.type = 'customer')", $query->getSQL());
    }

    /**
     * Tests adding an "OR" where condition
     */
    public function testOrWhere()
    {
        $query = new DeleteQuery("users");
        $query->where("id = 1")
            ->orWhere("name = 'dave'");
        $this->assertEquals("DELETE FROM users WHERE (id = 1) OR (name = 'dave')", $query->getSQL());
    }

    /**
     * Tests using an alias on our table name
     */
    public function testTableAlias()
    {
        $query = new DeleteQuery("users", "u");
        $this->assertEquals("DELETE FROM users AS u", $query->getSQL());
    }

    /**
     * Tests the "USING" expression
     */
    public function testUsing()
    {
        $query = new DeleteQuery("users");
        $query->using("emails")
            ->where("users.id = emails.userid AND emails.email = 'foo@bar.com'");
        $this->assertEquals("DELETE FROM users USING emails WHERE (users.id = emails.userid AND emails.email = 'foo@bar.com')", $query->getSQL());
    }

    /**
     * Tests adding a simple where clause
     */
    public function testWhere()
    {
        $query = new DeleteQuery("users");
        $query->where("id = 1");
        $this->assertEquals("DELETE FROM users WHERE (id = 1)", $query->getSQL());
    }
} 