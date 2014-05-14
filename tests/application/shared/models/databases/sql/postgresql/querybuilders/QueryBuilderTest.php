<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the query builder
 */
namespace RamODev\Application\Shared\Models\Databases\SQL\PostgreSQL\QueryBuilders;

class QueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that the query builder returns a DeleteQuery when we call delete()
     */
    public function testThatDeleteReturnsDeleteQueryBuilder()
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf("RamODev\\Application\\Shared\\Models\\Databases\\SQL\\PostgreSQL\\QueryBuilders\\DeleteQuery", $queryBuilder->delete("tableName", "tableAlias"));
    }

    /**
     * Tests that the query builder returns a InsertQuery when we call insert()
     */
    public function testThatInsertReturnsInsertQueryBuilder()
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf("RamODev\\Application\\Shared\\Models\\Databases\\SQL\\PostgreSQL\\QueryBuilders\\InsertQuery", $queryBuilder->insert("tableName", array("columnName" => "columnValue")));
    }

    /**
     * Tests that the query builder returns a SelectQuery when we call select()
     */
    public function testThatSelectReturnsSelectQueryBuilder()
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf("RamODev\\Application\\Shared\\Models\\Databases\\SQL\\PostgreSQL\\QueryBuilders\\SelectQuery", $queryBuilder->select("tableName", "tableAlias"));
    }

    /**
     * Tests that the query builder returns a UpdateQuery when we call update()
     */
    public function testThatUpdateReturnsUpdateQueryBuilder()
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf("RamODev\\Application\\Shared\\Models\\Databases\\SQL\\PostgreSQL\\QueryBuilders\\UpdateQuery", $queryBuilder->update("tableName", "tableAlias", array("columnName" => "columnValue")));
    }
} 