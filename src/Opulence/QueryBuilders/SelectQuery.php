<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\QueryBuilders;

use InvalidArgumentException;
use Opulence\QueryBuilders\Conditions\ICondition;

/**
 * Builds a select query
 */
class SelectQuery extends Query
{
    /** @var ConditionalQueryBuilder Handles functionality common to conditional queries */
    protected $conditionalQueryBuilder = null;
    /** @var array The list of select expressions */
    protected $selectExpressions = [];
    /** @var array The list of join statements */
    protected $joins = ["inner" => [], "left" => [], "right" => []];
    /** @var array The list of group by clauses */
    protected $groupByClauses = [];
    /** @var array The list of having conditions */
    protected $havingConditions = [];
    /** @var int|string $limit The number of rows to limit to */
    protected $limit = -1;
    /** @var int|string $offset The number of rows to offset by */
    protected $offset = -1;
    /** @var array The list of expressions to order by */
    protected $orderBy = [];

    /**
     * @param array $expression,... A variable list of select expressions
     */
    public function __construct(string ...$expression)
    {
        $this->selectExpressions = $expression;
        $this->conditionalQueryBuilder = new ConditionalQueryBuilder();
    }

    /**
     * Adds to a "GROUP BY" clause
     *
     * @param array $expression,... A variable list of expressions of what to group by
     * @return self For method chaining
     */
    public function addGroupBy(string ...$expression) : self
    {
        $this->groupByClauses = array_merge($this->groupByClauses, $expression);

        return $this;
    }

    /**
     * Adds to a "ORDER BY" clause
     *
     * @param array $expression,... A variable list of expressions to order by
     * @return self For method chaining
     */
    public function addOrderBy(string ...$expression) : self
    {
        $this->orderBy = array_merge($this->orderBy, $expression);

        return $this;
    }

    /**
     * Adds more select expressions
     *
     * @param array $expression,... A variable list of select expressions
     * @return self For method chaining
     */
    public function addSelectExpression(string ...$expression) : self
    {
        $this->selectExpressions = array_merge($this->selectExpressions, $expression);

        return $this;
    }

    /**
     * Adds to a "HAVING" condition that will be "AND"ed with other conditions
     *
     * @param array $conditions,... A variable list of conditions to be met
     * @return self For method chaining
     */
    public function andHaving(...$conditions) : self
    {
        $this->havingConditions = call_user_func_array(
            [$this->conditionalQueryBuilder, "addConditionToClause"],
            array_merge([$this->havingConditions, "AND"], $this->createConditionExpressions($conditions))
        );

        return $this;
    }

    /**
     * Adds to a "WHERE" condition that will be "AND"ed with other conditions
     *
     * @param array $conditions,... A variable list of conditions to be met
     * @return self For method chaining
     */
    public function andWhere(...$conditions) : self
    {
        call_user_func_array(
            [$this->conditionalQueryBuilder, "andWhere"], 
            $this->createConditionExpressions($conditions)
        );

        return $this;
    }

    /**
     * Specifies which table we're selecting from
     *
     * @param string $tableName The name of the table we're selecting from
     * @param string $tableAlias The alias of the table name
     * @return self For method chaining
     */
    public function from(string $tableName, string $tableAlias = "") : self
    {
        $this->setTable($tableName, $tableAlias);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSql() : string
    {
        // Build the selector
        $sql = "SELECT " . implode(", ", $this->selectExpressions)
            . (empty($this->tableName) ? "" : " FROM {$this->tableName}")
            . (empty($this->tableAlias) ? "" : " AS {$this->tableAlias}");

        // Add any joins
        foreach ($this->joins as $type => $joinsByType) {
            foreach ($joinsByType as $join) {
                $sql .= " " . strtoupper($type) . " JOIN {$join["tableName"]}"
                    . (empty($join["tableAlias"]) ? "" : " AS {$join["tableAlias"]}") . " ON {$join["condition"]}";
            }
        }

        // Add any conditions
        $sql .= $this->conditionalQueryBuilder->getClauseConditionSql("WHERE",
            $this->conditionalQueryBuilder->getWhereConditions());

        // Add groupings
        if (count($this->groupByClauses) > 0) {
            $sql .= " GROUP BY " . implode(", ", $this->groupByClauses);
        }

        // Add any groupings' conditions
        $sql .= $this->conditionalQueryBuilder->getClauseConditionSql("HAVING", $this->havingConditions);

        // Order the query
        if (count($this->orderBy) > 0) {
            $sql .= " ORDER BY " . implode(", ", $this->orderBy);
        }

        // Add a limit
        if ($this->limit !== -1) {
            $sql .= " LIMIT {$this->limit}";
        }

        // Add an offset
        if ($this->offset !== -1) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $sql;
    }

    /**
     * Starts a "GROUP BY" clause
     * Only call this method once per query because it will overwrite any previously-set "GROUP BY" expressions
     *
     * @param array $expression,... A variable list of expressions of what to group by
     * @return self For method chaining
     */
    public function groupBy(string ...$expression) : self
    {
        $this->groupByClauses = $expression;

        return $this;
    }

    /**
     * Starts a "HAVING" condition
     * Only call this method once per query because it will overwrite any previously-set "HAVING" expressions
     *
     * @param array $conditions,... A variable list of conditions to be met
     * @return self For method chaining
     */
    public function having(...$conditions) : self
    {
        // We want to wipe out anything already in the condition list
        $this->havingConditions = [];
        $this->havingConditions = call_user_func_array(
            [$this->conditionalQueryBuilder, "addConditionToClause"],
            array_merge([$this->havingConditions, "AND"], $this->createConditionExpressions($conditions))
        );

        return $this;
    }

    /**
     * Adds a inner join to the query
     *
     * @param string $tableName The name of the table we're joining
     * @param string $tableAlias The alias of the table name
     * @param string $condition The "ON" portion of the join
     * @return self For method chaining
     */
    public function innerJoin(string $tableName, string $tableAlias, string $condition) : self
    {
        $this->joins["inner"][] = ["tableName" => $tableName, "tableAlias" => $tableAlias, "condition" => $condition];

        return $this;
    }

    /**
     * Adds a join to the query
     * This is the same thing as an inner join
     *
     * @param string $tableName The name of the table we're joining
     * @param string $tableAlias The alias of the table name
     * @param string $condition The "ON" portion of the join
     * @return self For method chaining
     */
    public function join(string $tableName, string $tableAlias, string $condition) : self
    {
        return $this->innerJoin($tableName, $tableAlias, $condition);
    }

    /**
     * Adds a left join to the query
     *
     * @param string $tableName The name of the table we're joining
     * @param string $tableAlias The alias of the table name
     * @param string $condition The "ON" portion of the join
     * @return self For method chaining
     */
    public function leftJoin(string $tableName, string $tableAlias, string $condition) : self
    {
        $this->joins["left"][] = ["tableName" => $tableName, "tableAlias" => $tableAlias, "condition" => $condition];

        return $this;
    }

    /**
     * Limits the number of rows returned by the query
     *
     * @param int|string $numRows The number of rows to limit in the results
     *      or the name of the placeholder value that will contain the number of rows
     * @return self For method chaining
     */
    public function limit($numRows) : self
    {
        $this->limit = $numRows;

        return $this;
    }

    /**
     * Skips the input number of rows before returning rows
     *
     * @param int|string $numRows The number of rows to skip in the results
     *      or the name of the placeholder value that will contain the number of rows
     * @return self For method chaining
     */
    public function offset($numRows) : self
    {
        $this->offset = $numRows;

        return $this;
    }

    /**
     * Adds to a "HAVING" condition that will be "OR"ed with other conditions
     *
     * @param array $conditions,... A variable list of conditions to be met
     * @return self For method chaining
     */
    public function orHaving(...$conditions) : self
    {
        $this->havingConditions = call_user_func_array(
            [$this->conditionalQueryBuilder, "addConditionToClause"],
            array_merge([$this->havingConditions, "OR"], $this->createConditionExpressions($conditions))
        );

        return $this;
    }

    /**
     * Adds to a "WHERE" condition that will be "OR"ed with other conditions
     *
     * @param array $conditions,... A variable list of conditions to be met
     * @return self For method chaining
     */
    public function orWhere(...$conditions) : self
    {
        call_user_func_array(
            [$this->conditionalQueryBuilder, "orWhere"], 
            $this->createConditionExpressions($conditions)
        );

        return $this;
    }

    /**
     * Starts an "ORDER BY" clause
     * Only call this method once per query because it will overwrite any previously-set "ORDER BY" expressions
     *
     * @param array $expression,... A variable list of expressions to order by
     * @return self For method chaining
     */
    public function orderBy(string ...$expression) : self
    {
        $this->orderBy = $expression;

        return $this;
    }

    /**
     * Adds a right join to the query
     *
     * @param string $tableName The name of the table we're joining
     * @param string $tableAlias The alias of the table name
     * @param string $condition The "ON" portion of the join
     * @return self For method chaining
     */
    public function rightJoin(string $tableName, string $tableAlias, string $condition) : self
    {
        $this->joins["right"][] = ["tableName" => $tableName, "tableAlias" => $tableAlias, "condition" => $condition];

        return $this;
    }

    /**
     * Starts a "WHERE" condition
     * Only call this method once per query because it will overwrite any previously-set "WHERE" expressions
     *
     * @param array $conditions,... A variable list of conditions to be met
     * @return self For method chaining
     */
    public function where(...$conditions) : self
    {
        call_user_func_array(
            [$this->conditionalQueryBuilder, "where"], 
            $this->createConditionExpressions($conditions)
        );

        return $this;
    }
    
    /**
     * Converts a list of condition strings or objects to their string representations
     * 
     * @param array $conditions The list of strings of condition objects to convert
     * @return array The list of condition expressions
     */
    private function createConditionExpressions(array $conditions) : array
    {
        $conditionExpressions = [];
        
        foreach ($conditions as $condition) {
            if ($condition instanceof ICondition) {
                $this->addUnnamedPlaceholderValues($condition->getParameters());
                $conditionExpressions[] = $condition->getSql();
            } elseif (is_string($condition)) {
                $conditionExpressions[] = $condition;
            } else {
                throw new InvalidArgumentException("Condition must either be string or ICondition object");
            }
        }
        
        return $conditionExpressions;
    }
}