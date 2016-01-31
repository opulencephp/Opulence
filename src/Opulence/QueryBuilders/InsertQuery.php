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
 * Builds an insert query
 */
class InsertQuery extends Query
{
    /** @var AugmentingQueryBuilder Handles functionality common to augmenting queries */
    protected $augmentingQueryBuilder = null;

    /**
     * @param string $tableName The name of the table we're inserting into
     * @param array $columnNamesToValues The mapping of column names to their respective values
     * @throws InvalidQueryException Thrown if the query is invalid
     */
    public function __construct(string $tableName, array $columnNamesToValues)
    {
        $this->tableName = $tableName;
        $this->augmentingQueryBuilder = new AugmentingQueryBuilder();
        $this->addColumnValues($columnNamesToValues);
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

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSql() : string
    {
        $sql = "INSERT INTO {$this->tableName}"
            . " (" . implode(", ", array_keys($this->augmentingQueryBuilder->getColumnNamesToValues())) . ") VALUES ("
            . implode(", ",
                array_fill(0, count(array_values($this->augmentingQueryBuilder->getColumnNamesToValues())), "?"))
            . ")";

        return $sql;
    }

    /**
     * @inheritdoc
     */
    public function setTable(string $tableName, string $tableAlias = "")
    {
        parent::setTable($tableName);
    }
} 