<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Builds parts of a query that can use a "WHERE" clause
 */
namespace Opulence\QueryBuilders;

class ConditionalQueryBuilder
{
    /** @var array The list of WHERE expressions */
    protected $whereConditions = [];

    /**
     * Adds a condition to a clause
     *
     * @param array $clauseConditions The list of conditions that already belong to the clause
     * @param string $operation Either "AND" or "OR", indicating how this condition is being added to the list of conditions
     * @param string $condition,... A variable list of conditions to be met
     * @return array The input array with the condition added
     */
    public function addConditionToClause(array $clauseConditions, $operation, $condition)
    {
        // This will grab just the list of conditions
        $conditions = array_slice(func_get_args(), 2);

        foreach($conditions as $condition)
        {
            $clauseConditions[] = ["operation" => $operation, "condition" => $condition];
        }

        return $clauseConditions;
    }

    /**
     * Adds to a "WHERE" condition that will be "AND"ed with other conditions
     *
     * @param string $condition,... A variable list of conditions to be met
     * @return $this
     */
    public function andWhere($condition)
    {
        $this->whereConditions = call_user_func_array(
            [$this, "addConditionToClause"],
            array_merge([$this->whereConditions, "AND"], func_get_args())
        );

        return $this;
    }

    /**
     * Gets the SQL that makes up a clause that permits boolean operations ie "AND" and "OR"
     *
     * @param string $conditionType The name of the condition type ie "WHERE"
     * @param array $clauseConditions The array of condition data whose SQL we want
     * @return string The SQL that makes up the input clause(s)
     */
    public function getClauseConditionSQL($conditionType, array $clauseConditions)
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
            $sql .= ($haveAddedAClause ? " " . strtoupper($conditionData["operation"]) : "")
                . " (" . $conditionData["condition"] . ")";
            $haveAddedAClause = true;
        }

        return $sql;
    }

    /**
     * @return array
     */
    public function getWhereConditions()
    {
        return $this->whereConditions;
    }

    /**
     * Adds to a "WHERE" condition that will be "OR"ed with other conditions
     *
     * @param string $condition,... A variable list of conditions to be met
     * @return $this
     */
    public function orWhere($condition)
    {
        $this->whereConditions = call_user_func_array([$this, "addConditionToClause"],
            array_merge([$this->whereConditions, "OR"], func_get_args()));

        return $this;
    }

    /**
     * Starts a "WHERE" condition
     * Only call this method once per query because it will overwrite any previously-set "WHERE" expressions
     *
     * @param string $condition,... A variable list of conditions to be met
     * @return $this
     */
    public function where($condition)
    {
        // We want to wipe out anything already in the condition list
        $this->whereConditions = [];
        $this->whereConditions = call_user_func_array([$this, "addConditionToClause"],
            array_merge([$this->whereConditions, "AND"], func_get_args()));

        return $this;
    }
} 