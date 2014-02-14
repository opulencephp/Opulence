<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the query builder
 */
namespace RamODev\Databases\RDBMS\PostgreSQL\QueryBuilders;

require_once(__DIR__ . "/../../../../../databases/rdbms/postgresql/querybuilders/QueryBuilder.php");

class QueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that our query builder returns a DeleteQuery when we call delete()
     */
    public function testThatDeleteReturnsDeleteQueryBuilder()
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf("\\RamODev\\Databases\\RDBMS\\PostgreSQL\\QueryBuilders\\DeleteQuery", $queryBuilder->delete("tableName", "tableAlias"));
    }

    /**
     * Tests that our query builder returns a InsertQuery when we call insert()
     */
    public function testThatInsertReturnsInsertQueryBuilder()
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf("\\RamODev\\Databases\\RDBMS\\PostgreSQL\\QueryBuilders\\InsertQuery", $queryBuilder->insert("tableName", array("columnName" => "columnValue")));
    }

    /**
     * Tests that our query builder returns a SelectQuery when we call select()
     */
    public function testThatSelectReturnsSelectQueryBuilder()
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf("\\RamODev\\Databases\\RDBMS\\PostgreSQL\\QueryBuilders\\SelectQuery", $queryBuilder->select("tableName", "tableAlias"));
    }

    /**
     * Tests that our query builder returns a UpdateQuery when we call update()
     */
    public function testThatUpdateReturnsUpdateQueryBuilder()
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf("\\RamODev\\Databases\\RDBMS\\PostgreSQL\\QueryBuilders\\UpdateQuery", $queryBuilder->update("tableName", "tableAlias", array("columnName" => "columnValue")));
    }
} 