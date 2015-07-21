<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Builds a delete query
 */
namespace Opulence\QueryBuilders;

class DeleteQuery extends Query
{
    /** @var array The list of table expressions, allowing columns from other table to appear in the WHERE condition */
    protected $usingExpressions = [];
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
        call_user_func_array([$this->conditionalQueryBuilder, "andWhere"], func_get_args());

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSQL()
    {
        $sql = "DELETE FROM " . $this->tableName . (empty($this->tableAlias) ? "" : " AS " . $this->tableAlias);

        if(count($this->usingExpressions) > 0)
        {
            $sql .= " USING " . implode(", ", $this->usingExpressions);
        }

        // Add any conditions
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
        call_user_func_array([$this->conditionalQueryBuilder, "orWhere"], func_get_args());

        return $this;
    }

    /**
     * Starts a "USING" expression
     * Only call this method once per query because it will overwrite any previously-set "USING" expressions
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
     * Only call this method once per query because it will overwrite any previously-set "WHERE" expressions
     *
     * @param string $condition,... A variable list of conditions to be met
     * @return $this
     */
    public function where($condition)
    {
        call_user_func_array([$this->conditionalQueryBuilder, "where"], func_get_args());

        return $this;
    }
} 