<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the query builder
 */
namespace RDev\Databases\SQL\QueryBuilders\MySQL;

class QueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that the query builder returns a DeleteQuery when we call delete()
     */
    public function testThatDeleteReturnsDeleteQueryBuilder()
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf("RDev\\Databases\\SQL\\QueryBuilders\\MySQL\\DeleteQuery",
            $queryBuilder->delete("tableName", "tableAlias"));
    }

    /**
     * Tests that the query builder returns a InsertQuery when we call insert()
     */
    public function testThatInsertReturnsInsertQueryBuilder()
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf("RDev\\Databases\\SQL\\QueryBuilders\\MySQL\\InsertQuery",
            $queryBuilder->insert("tableName", ["columnName" => "columnValue"]));
    }

    /**
     * Tests that the query builder returns a SelectQuery when we call select()
     */
    public function testThatSelectReturnsSelectQueryBuilder()
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf("RDev\\Databases\\SQL\\QueryBuilders\\MySQL\\SelectQuery",
            $queryBuilder->select("tableName", "tableAlias"));
    }

    /**
     * Tests that the query builder returns a UpdateQuery when we call update()
     */
    public function testThatUpdateReturnsUpdateQueryBuilder()
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf("RDev\\Databases\\SQL\\QueryBuilders\\MySQL\\UpdateQuery",
            $queryBuilder->update("tableName", "tableAlias", ["columnName" => "columnValue"]));
    }
} 