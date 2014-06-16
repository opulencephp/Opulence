<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the query builder
 */
namespace RDev\Models\Databases\SQL\QueryBuilders\PostgreSQL;

class QueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that the query builder returns a DeleteQuery when we call delete()
     */
    public function testThatDeleteReturnsDeleteQueryBuilder()
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\QueryBuilders\\PostgreSQL\\DeleteQuery",
            $queryBuilder->delete("tableName", "tableAlias"));
    }

    /**
     * Tests that the query builder returns a InsertQuery when we call insert()
     */
    public function testThatInsertReturnsInsertQueryBuilder()
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\QueryBuilders\\PostgreSQL\\InsertQuery",
            $queryBuilder->insert("tableName", ["columnName" => "columnValue"]));
    }

    /**
     * Tests that the query builder returns a SelectQuery when we call select()
     */
    public function testThatSelectReturnsSelectQueryBuilder()
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\QueryBuilders\\PostgreSQL\\SelectQuery",
            $queryBuilder->select("tableName", "tableAlias"));
    }

    /**
     * Tests that the query builder returns a UpdateQuery when we call update()
     */
    public function testThatUpdateReturnsUpdateQueryBuilder()
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf("RDev\\Models\\Databases\\SQL\\QueryBuilders\\PostgreSQL\\UpdateQuery",
            $queryBuilder->update("tableName", "tableAlias", ["columnName" => "columnValue"]));
    }
} 