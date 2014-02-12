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
 * Programmatically builds up a database query
 */
namespace Storage\Databases\QueryBuilders;

require_once(__DIR__ . "/exceptions/InvalidQueryException.php");

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
    abstract public function insert($tableName, $columnNamesToValues);

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
    abstract public function update($tableName, $alias, $columnNamesToValues);
}