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
 * Programmatically builds up a PostgreSQL query
 */
namespace RamODev\Databases\RDBMS\MySQL\QueryBuilders;
use RamODev\Databases\RDBMS\QueryBuilders;

require_once(__DIR__ . "/../../querybuilders/QueryBuilder.php");
require_once(__DIR__ . "/DeleteQuery.php");
require_once(__DIR__ . "/InsertQuery.php");
require_once(__DIR__ . "/SelectQuery.php");
require_once(__DIR__ . "/UpdateQuery.php");

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
    public function insert($tableName, $columnNamesToValues)
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
        $queryClass = new \ReflectionClass("\\RamODev\\Databases\\RDBMS\\MySQL\\QueryBuilders\\SelectQuery");

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
    public function update($tableName, $alias, $columnNamesToValues)
    {
        return new UpdateQuery($tableName, $alias, $columnNamesToValues);
    }
} 