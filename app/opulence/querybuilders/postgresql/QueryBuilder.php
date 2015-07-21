<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Programmatically builds up a PostgreSQL query
 */
namespace Opulence\QueryBuilders\PostgreSQL;
use ReflectionClass;
use Opulence\QueryBuilders\QueryBuilder as BaseQueryBuilder;

class QueryBuilder extends BaseQueryBuilder
{
    /**
     * @inheritdoc
     */
    public function delete($tableName, $alias = "")
    {
        return new DeleteQuery($tableName, $alias);
    }

    /**
     * @inheritdoc
     */
    public function insert($tableName, array $columnNamesToValues)
    {
        return new InsertQuery($tableName, $columnNamesToValues);
    }

    /**
     * @inheritdoc
     * @return SelectQuery
     */
    public function select($expression)
    {
        // This code allows us to pass a variable list of parameters to a class constructor
        $queryClass = new ReflectionClass(SelectQuery::class);

        return $queryClass->newInstanceArgs(func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function update($tableName, $alias, array $columnNamesToValues)
    {
        return new UpdateQuery($tableName, $alias, $columnNamesToValues);
    }
} 