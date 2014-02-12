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
 * Tests the insert query
 */
namespace Storage\Databases\PostgreSQL\QueryBuilders;

require_once(__DIR__ . "/../../../../../storage/databases/postgresql/querybuilders/InsertQuery.php");

class InsertQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding a column to return
     */
    public function testAddReturning()
    {
        $query = new InsertQuery("users", array("name" => "dave"));
        $query->returning("id")
            ->addReturning("name");
        $this->assertEquals("INSERT INTO users (name) VALUES (?) RETURNING id, name", $query->getSQL());
        $this->assertEquals(array("dave"), $query->getParameters());
    }

    /**
     * Tests all our methods in a single, complicated query
     */
    public function testEverything()
    {
        $query = new InsertQuery("users", array("name" => "dave"));
        $query->addColumnValues(array("email" => "foo@bar.com"))
            ->returning("id")
            ->addReturning("name");
        $this->assertEquals("INSERT INTO users (name, email) VALUES (?, ?) RETURNING id, name", $query->getSQL());
        $this->assertEquals(array("dave", "foo@bar.com"), $query->getParameters());
    }

    /**
     * Tests returning a column value
     */
    public function testReturning()
    {
        $query = new InsertQuery("users", array("name" => "dave"));
        $query->returning("id", "name");
        $this->assertEquals("INSERT INTO users (name) VALUES (?) RETURNING id, name", $query->getSQL());
        $this->assertEquals(array("dave"), $query->getParameters());
    }
} 