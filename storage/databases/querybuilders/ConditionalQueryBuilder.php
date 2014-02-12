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
 * Builds parts of a query that can use a "WHERE" clause
 */
namespace Storage\Databases\QueryBuilders;

require_once(__DIR__ . "/Query.php");

class ConditionalQueryBuilder
{
    /** @var array The list of WHERE expressions */
    protected $whereConditions = array();

    /**
     * Adds a condition to a clause
     *
     * @param array $clauseConditions The list of conditions that already belong to the clause
     * @param string $operation Either "AND" or "OR", indicating how this condition is being added to the list of conditions
     * @param string $condition,... A variable list of conditions to be met
     * @returns array The input array with our condition added
     */
    public function addConditionToClause($clauseConditions, $operation, $condition)
    {
        // This will grab just the list of conditions
        $conditions = array_slice(func_get_args(), 2);

        foreach($conditions as $condition)
        {
            $clauseConditions[] = array("operation" => $operation, "condition" => $condition);
        }

        return $clauseConditions;
    }

    /**
     * Adds to a "WHERE" condition that will be "AND"ed with other conditions
     *
     * @param string $condition,... A variable list of conditions to be met
     * @returns $this
     */
    public function andWhere($condition)
    {
        $this->whereConditions = call_user_func_array(array($this, "addConditionToClause"), array_merge(array($this->whereConditions, "AND"), func_get_args()));

        return $this;
    }

    /**
     * Gets the SQL that makes up a clause that permits boolean operations ie "AND" and "OR"
     *
     * @param string $conditionType The name of the condition type ie "WHERE"
     * @param array $clauseConditions The array of condition data whose SQL we want
     * @return string The SQL that makes up the input clause(s)
     */
    public function getClauseConditionSQL($conditionType, $clauseConditions)
    {
        if(count($clauseConditions) === 0)
        {
            return "";
        }

        $sql = " " . strtoupper($conditionType);
        // This will help us keep track of whether or not we've added at least one clause
        $haveAddedAClause = false;

        foreach($clauseConditions as $conditionData)
        {
            $sql .= ($haveAddedAClause ? " " . strtoupper($conditionData["operation"]) : "") . " (" . $conditionData["condition"] . ")";
            $haveAddedAClause = true;
        }

        return $sql;
    }

    /**
     * @returns array
     */
    public function getWhereConditions()
    {
        return $this->whereConditions;
    }

    /**
     * Adds to a "WHERE" condition that will be "OR"ed with other conditions
     *
     * @param string $condition,... A variable list of conditions to be met
     * @returns $this
     */
    public function orWhere($condition)
    {
        $this->whereConditions = call_user_func_array(array($this, "addConditionToClause"), array_merge(array($this->whereConditions, "OR"), func_get_args()));

        return $this;
    }

    /**
     * Starts a "WHERE" condition
     * Only call this method once per query because it will overwrite an previously-set "WHERE" expressions
     *
     * @param string $condition,... A variable list of conditions to be met
     * @returns $this
     */
    public function where($condition)
    {
        // We want to wipe out anything already in the condition list
        $this->whereConditions = array();
        $this->whereConditions = call_user_func_array(array($this, "addConditionToClause"), array_merge(array($this->whereConditions, "AND"), func_get_args()));

        return $this;
    }
} 