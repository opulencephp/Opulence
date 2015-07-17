<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Programmatically builds up a database query
 */
namespace Opulence\QueryBuilders;

abstract class QueryBuilder
{
    /**
     * Starts a new delete query
     *
     * @param string $tableName The name of the table we're deleting from
     * @param string $alias The alias of the table name
     * @return DeleteQuery The delete query builder
     */
    abstract public function delete($tableName, $alias = "");

    /**
     * Starts a new insert query
     *
     * @param string $tableName The name of the table we're inserting into
     * @param array $columnNamesToValues The mapping of column names to their respective values
     * @return InsertQuery The insert query builder
     */
    abstract public function insert($tableName, array $columnNamesToValues);

    /**
     * Starts a new select query
     *
     * @param string $expression,... A variable list of select expressions
     * @return SelectQuery The select query builder
     */
    abstract public function select($expression);

    /**
     * Starts a new update query
     *
     * @param string $tableName The name of the table we're updating
     * @param string $alias The alias of the table name
     * @param array $columnNamesToValues The mapping of column names to their respective values
     * @return UpdateQuery The update query builder
     */
    abstract public function update($tableName, $alias, array $columnNamesToValues);
}