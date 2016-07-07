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
 * Builds a delete query
 */
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
    public function __construct(string $tableName, string $tableAlias = "")
    {
        $this->tableName = $tableName;
        $this->tableAlias = $tableAlias;
        $this->conditionalQueryBuilder = new ConditionalQueryBuilder();
    }

    /**
     * Adds to a "USING" expression
     *
     * @param array $expression,... A variable list of other tables' names to use in the WHERE condition
     * @return self For method chaining
     */
    public function addUsing(string ...$expression) : self
    {
        $this->usingExpressions = array_merge($this->usingExpressions, $expression);

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
     * @inheritdoc
     */
    public function getSql() : string
    {
        $sql = "DELETE FROM {$this->tableName}" . (empty($this->tableAlias) ? "" : " AS {$this->tableAlias}");

        if (count($this->usingExpressions) > 0) {
            $sql .= " USING " . implode(", ", $this->usingExpressions);
        }

        // Add any conditions
        $sql .= $this->conditionalQueryBuilder->getClauseConditionSql("WHERE",
            $this->conditionalQueryBuilder->getWhereConditions());

        return $sql;
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
     * Starts a "USING" expression
     * Only call this method once per query because it will overwrite any previously-set "USING" expressions
     *
     * @param array $expression,... A variable list of other tables' names to use in the WHERE condition
     * @return self For method chaining
     */
    public function using(string ...$expression) : self
    {
        $this->usingExpressions = $expression;

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