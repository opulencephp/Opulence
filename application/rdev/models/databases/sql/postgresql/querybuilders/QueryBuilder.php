<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Programmatically builds up a PostgreSQL query
 */
namespace RDev\Models\Databases\SQL\PostgreSQL\QueryBuilders;
use RDev\Models\Databases\SQL\QueryBuilders;

class QueryBuilder extends QueryBuilders\QueryBuilder
{
    /**
     * {@inheritdoc}
     */
    public function delete($tableName, $alias = "")
    {
        return new DeleteQuery($tableName, $alias);
    }

    /**
     * {@inheritdoc}
     */
    public function insert($tableName, array $columnNamesToValues)
    {
        return new InsertQuery($tableName, $columnNamesToValues);
    }

    /**
     * {@inheritdoc}
     */
    public function select($expression)
    {
        // This code allows us to pass a variable list of parameters to a class constructor
        $queryClass = new \ReflectionClass("RDev\\Models\\Databases\\SQL\\PostgreSQL\\QueryBuilders\\SelectQuery");

        return $queryClass->newInstanceArgs(func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function update($tableName, $alias, array $columnNamesToValues)
    {
        return new UpdateQuery($tableName, $alias, $columnNamesToValues);
    }
} 