<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\QueryBuilders;

/**
 * Builds an update query
 */
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
     * @throws InvalidQueryException Thrown if the query is invalid
     */
    public function __construct(string $tableName, string $tableAlias, array $columnNamesToValues)
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
     *      Optionally, the values can be contained in an array whose first item is the value and whose second value is
     *      the PDO constant indicating the type of data the value represents
     * @return self For method chaining
     * @throws InvalidQueryException Thrown if the query is invalid
     */
    public function addColumnValues(array $columnNamesToValues) : self
    {
        if (count($columnNamesToValues) > 0) {
            $this->addUnnamedPlaceholderValues(array_values($columnNamesToValues));

            // The augmenting query doesn't care about the data type, so get rid of it
            $columnNamesToValuesWithoutDataTypes = [];

            foreach ($columnNamesToValues as $name => $value) {
                if (is_array($value)) {
                    $columnNamesToValuesWithoutDataTypes[$name] = $value[0];
                } else {
                    $columnNamesToValuesWithoutDataTypes[$name] = $value;
                }
            }

            $this->augmentingQueryBuilder->addColumnValues($columnNamesToValuesWithoutDataTypes);
        }

        return $this;
    }

    /**
     * Adds to a "WHERE" condition that will be "AND"ed with other conditions
     *
     * @param array $condition,... A variable list of conditions to be met
     * @return self For method chaining
     */
    public function andWhere(string ...$condition) : self
    {
        call_user_func_array([$this->conditionalQueryBuilder, "andWhere"], $condition);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSql() : string
    {
        $sql = "UPDATE " . $this->tableName . (empty($this->tableAlias) ? "" : " AS " . $this->tableAlias) . " SET";

        foreach ($this->augmentingQueryBuilder->getColumnNamesToValues() as $columnName => $value) {
            $sql .= " " . $columnName . " = ?,";
        }

        $sql = trim($sql, ",");
        // Add any conditions
        $sql .= $this->conditionalQueryBuilder
            ->getClauseConditionSql("WHERE", $this->conditionalQueryBuilder->getWhereConditions());

        return $sql;
    }

    /**
     * Adds to a "WHERE" condition that will be "OR"ed with other conditions
     *
     * @param array $condition,... A variable list of conditions to be met
     * @return self For method chaining
     */
    public function orWhere(string ...$condition) : self
    {
        call_user_func_array([$this->conditionalQueryBuilder, "orWhere"], $condition);

        return $this;
    }

    /**
     * Starts a "WHERE" condition
     * Only call this method once per query because it will overwrite any previously-set "WHERE" expressions
     *
     * @param array $condition,... A variable list of conditions to be met
     * @return self For method chaining
     */
    public function where(string ...$condition) : self
    {
        call_user_func_array([$this->conditionalQueryBuilder, "where"], $condition);

        return $this;
    }
} 