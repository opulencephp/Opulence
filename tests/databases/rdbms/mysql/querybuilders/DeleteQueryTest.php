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
namespace RamODev\Databases\RDBMS\MySQL\QueryBuilders;

require_once(__DIR__ . "/../../../../../databases/rdbms/mysql/querybuilders/DeleteQuery.php");

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
     * Tests all our methods in a single, complicated query
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