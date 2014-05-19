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
     * Starts a new delete query
     *
     * @param string $tableName The name of the table we're deleting from
     * @param string $alias The alias of the table name
     * @return DeleteQuery The delete query builder
     */
    public function delete($tableName, $alias = "")
    {
        return new DeleteQuery($tableName, $alias);
    }

    /**
     * Starts a new insert query
     *
     * @param string $tableName The name of the table we're inserting into
     * @param array $columnNamesToValues The mapping of column names to their respective values
     * @return InsertQuery The insert query builder
     */
    public function insert($tableName, array $columnNamesToValues)
    {
        return new InsertQuery($tableName, $columnNamesToValues);
    }

    /**
     * Starts a new select query
     *
     * @param string $expression,... A variable list of select expressions
     * @return SelectQuery The select query builder
     */
    public function select($expression)
    {
        // This code allows us to pass a variable list of parameters to a class constructor
        $queryClass = new \ReflectionClass("RDev\\Models\\Databases\\SQL\\PostgreSQL\\QueryBuilders\\SelectQuery");

        return $queryClass->newInstanceArgs(func_get_args());
    }

    /**
     * Starts a new update query
     *
     * @param string $tableName The name of the table we're updating
     * @param string $alias The alias of the table name
     * @param array $columnNamesToValues The mapping of column names to their respective values
     * @return UpdateQuery The update query builder
     */
    public function update($tableName, $alias, array $columnNamesToValues)
    {
        return new UpdateQuery($tableName, $alias, $columnNamesToValues);
    }
} 