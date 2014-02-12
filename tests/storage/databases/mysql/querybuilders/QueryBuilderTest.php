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
 * Tests the query builder
 */
namespace RamODev\Storage\Databases\MySQL\QueryBuilders;

require_once(__DIR__ . "/../../../../../storage/databases/mysql/querybuilders/QueryBuilder.php");

class QueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that our query builder returns a DeleteQuery when we call delete()
     */
    public function testThatDeleteReturnsDeleteQueryBuilder()
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf("\\RamODev\\Storage\\Databases\\MySQL\\QueryBuilders\\DeleteQuery", $queryBuilder->delete("tableName", "tableAlias"));
    }

    /**
     * Tests that our query builder returns a InsertQuery when we call insert()
     */
    public function testThatInsertReturnsInsertQueryBuilder()
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf("\\RamODev\\Storage\\Databases\\MySQL\\QueryBuilders\\InsertQuery", $queryBuilder->insert("tableName", array("columnName" => "columnValue")));
    }

    /**
     * Tests that our query builder returns a SelectQuery when we call select()
     */
    public function testThatSelectReturnsSelectQueryBuilder()
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf("\\RamODev\\Storage\\Databases\\MySQL\\QueryBuilders\\SelectQuery", $queryBuilder->select("tableName", "tableAlias"));
    }

    /**
     * Tests that our query builder returns a UpdateQuery when we call update()
     */
    public function testThatUpdateReturnsUpdateQueryBuilder()
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf("\\RamODev\\Storage\\Databases\\MySQL\\QueryBuilders\\UpdateQuery", $queryBuilder->update("tableName", "tableAlias", array("columnName" => "columnValue")));
    }
} 