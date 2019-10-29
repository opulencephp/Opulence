<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\QueryBuilders;

/**
 * Programmatically builds up a database query
 */
abstract class QueryBuilder
{
    /**
     * Starts a new delete query
     *
     * @param string $tableName The name of the table we're deleting from
     * @param string $alias The alias of the table name
     * @return DeleteQuery The delete query builder
     */
    abstract public function delete(string $tableName, string $alias = ''): DeleteQuery;

    /**
     * Starts a new insert query
     *
     * @param string $tableName The name of the table we're inserting into
     * @param array $columnNamesToValues The mapping of column names to their respective values
     * @return InsertQuery The insert query builder
     */
    abstract public function insert(string $tableName, array $columnNamesToValues): InsertQuery;

    /**
     * Starts a new select query
     *
     * @param string  ...$expression A variable list of select expressions
     * @return SelectQuery The select query builder
     */
    abstract public function select(string ...$expression): SelectQuery;

    /**
     * Starts a new update query
     *
     * @param string $tableName The name of the table we're updating
     * @param string $alias The alias of the table name
     * @param array $columnNamesToValues The mapping of column names to their respective values
     * @return UpdateQuery The update query builder
     */
    abstract public function update(string $tableName, string $alias, array $columnNamesToValues): UpdateQuery;
}
