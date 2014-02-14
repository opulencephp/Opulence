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
 * Builds a delete query
 */
namespace RamODev\Databases\RDBMS\QueryBuilders;

require_once(__DIR__ . "/Query.php");
require_once(__DIR__ . "/ConditionalQueryBuilder.php");

class DeleteQuery extends Query
{
    /** @var array The list of table expressions, allowing columns from other table to appear in the WHERE condition */
    protected $usingExpressions = array();
    /** @var ConditionalQueryBuilder Handles functionality common to conditional queries */
    protected $conditionalQueryBuilder = null;

    /**
     * @param string $tableName The name of the table we're querying
     * @param string $tableAlias The alias of the table we're querying
     */
    public function __construct($tableName, $tableAlias = "")
    {
        $this->tableName = $tableName;
        $this->tableAlias = $tableAlias;
        $this->conditionalQueryBuilder = new ConditionalQueryBuilder();
    }

    /**
     * Adds to a "USING" expression
     *
     * @param string $expression,... A variable list of other tables' names to use in the WHERE condition
     * @return $this
     */
    public function addUsing($expression)
    {
        $this->usingExpressions = array_merge($this->usingExpressions, func_get_args());

        return $this;
    }

    /**
     * Adds to a "WHERE" condition that will be "AND"ed with other conditions
     *
     * @param string $condition,... A variable list of conditions to be met
     * @return $this
     */
    public function andWhere($condition)
    {
        call_user_func_array(array($this->conditionalQueryBuilder, "andWhere"), func_get_args());

        return $this;
    }

    /**
     * Gets the SQL statement as a string
     *
     * @return string The SQL statement
     */
    public function getSQL()
    {
        $sql = "DELETE FROM " . $this->tableName . (empty($this->tableAlias) ? "" : " AS " . $this->tableAlias);

        if(count($this->usingExpressions) > 0)
        {
            $sql .= " USING " . implode(", ", $this->usingExpressions);
        }

        $sql .= $this->conditionalQueryBuilder->getClauseConditionSQL("WHERE", $this->conditionalQueryBuilder->getWhereConditions());

        return $sql;
    }

    /**
     * Adds to a "WHERE" condition that will be "OR"ed with other conditions
     *
     * @param string $condition,... A variable list of conditions to be met
     * @return $this
     */
    public function orWhere($condition)
    {
        call_user_func_array(array($this->conditionalQueryBuilder, "orWhere"), func_get_args());

        return $this;
    }

    /**
     * Starts a "USING" expression
     * Only call this method once per query because it will overwrite an previously-set "USING" expressions
     *
     * @param string $expression,... A variable list of other tables' names to use in the WHERE condition
     * @return $this
     */
    public function using($expression)
    {
        $this->usingExpressions = func_get_args();

        return $this;
    }

    /**
     * Starts a "WHERE" condition
     * Only call this method once per query because it will overwrite an previously-set "WHERE" expressions
     *
     * @param string $condition,... A variable list of conditions to be met
     * @return $this
     */
    public function where($condition)
    {
        call_user_func_array(array($this->conditionalQueryBuilder, "where"), func_get_args());

        return $this;
    }
} 