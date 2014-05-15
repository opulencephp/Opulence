<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Builds an insert query
 */
namespace RDev\Application\Shared\Models\Databases\SQL\QueryBuilders;
use RDev\Application\Shared\Models\Databases\SQL\QueryBuilders\Exceptions;

class InsertQuery extends Query
{
    /** @var AugmentingQueryBuilder Handles functionality common to augmenting queries */
    protected $augmentingQueryBuilder = null;

    /**
     * @param string $tableName The name of the table we're inserting into
     * @param array $columnNamesToValues The mapping of column names to their respective values
     * @throws Exceptions\InvalidQueryException Thrown if the query is invalid
     */
    public function __construct($tableName, array $columnNamesToValues)
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
     * @return $this
     * @throws Exceptions\InvalidQueryException Thrown if the query is invalid
     */
    public function addColumnValues(array $columnNamesToValues)
    {
        $this->addUnnamedPlaceholderValues(array_values($columnNamesToValues));

        // The augmenting query doesn't care about the data type, so get rid of it
        $columnNamesToValuesWithoutDataTypes = array();

        foreach($columnNamesToValues as $name => $value)
        {
            if(is_array($value))
            {
                $columnNamesToValuesWithoutDataTypes[$name] = $value[0];
            }
            else
            {
                $columnNamesToValuesWithoutDataTypes[$name] = $value;
            }
        }

        $this->augmentingQueryBuilder->addColumnValues($columnNamesToValuesWithoutDataTypes);

        return $this;
    }

    /**
     * Gets the SQL statement as a string
     *
     * @return string The SQL statement
     */
    public function getSQL()
    {
        $sql = "INSERT INTO " . $this->tableName
            . " (" . implode(", ", array_keys($this->augmentingQueryBuilder->getColumnNamesToValues())) . ") VALUES ("
            . implode(", ", array_fill(0, count(array_values($this->augmentingQueryBuilder->getColumnNamesToValues())), "?"))
            . ")";

        return $sql;
    }

    /**
     * Sets the table we're querying
     *
     * @param string $tableName The name of the table we're querying
     */
    public function setTable($tableName)
    {
        parent::setTable($tableName);
    }
} 