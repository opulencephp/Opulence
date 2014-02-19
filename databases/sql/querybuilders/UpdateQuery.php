<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Builds an update query
 */
namespace RamODev\Databases\SQL\QueryBuilders;

require_once(__DIR__ . "/Query.php");
require_once(__DIR__ . "/ConditionalQueryBuilder.php");

class UpdateQuery extends Query
{
    /** @var AugmentingQueryBuilder Handles functionality common to augmenting queries */
    protected $augmentingQueryBuilder = null;
    /** @var ConditionalQueryBuilder Handles functionality common to conditional queries */
    protected $conditionalQueryBuilder = null;

    /**
     * @param string $tableName The name of the table we're querying
     * @param string $tableAlias The alias of the table we're querying
     * @param array $columnNamesToValues The mapping of column names to their respective values
     */
    public function __construct($tableName, $tableAlias, $columnNamesToValues)
    {
        $this->setTable($tableName, $tableAlias);
        $this->augmentingQueryBuilder = new AugmentingQueryBuilder();
        $this->addColumnValues($columnNamesToValues);
        $this->conditionalQueryBuilder = new ConditionalQueryBuilder();
    }

    /**
     * Adds column values to the query
     *
     * @param array $columnNamesToValues The mapping of column names to their respective values
     * @return $this
     */
    public function addColumnValues($columnNamesToValues)
    {
        if(count($columnNamesToValues) > 0)
        {
            $this->addUnnamedPlaceholderValues(array_values($columnNamesToValues));
            $this->augmentingQueryBuilder->addColumnValues($columnNamesToValues);
        }

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
        $sql = "UPDATE " . $this->tableName . (empty($this->tableAlias) ? "" : " AS " . $this->tableAlias) . " SET";

        foreach($this->augmentingQueryBuilder->getColumnNamesToValues() as $columnName => $value)
        {
            $sql .= " " . $columnName . " = ?,";
        }

        $sql = trim($sql, ",");
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
        call_user_func_array(array($this->conditionalQueryBuilder, "orWhere"), func_get_args());

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