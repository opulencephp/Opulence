<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines common functionality for query classes
 */
namespace Opulence\QueryBuilders;
use PDO;

abstract class Query
{
    /** @var string The name of the table we're querying */
    protected $tableName = "";
    /** @var string The alias of the table we're querying */
    protected $tableAlias = "";
    /** @var array The list of bound parameters */
    protected $parameters = [];
    /**
     * True if we're using unnamed placeholders instead of named placeholders
     * False if we're using named placeholders instead of unnamed placeholders
     * Null if we haven't added any placeholders, and, therefore, don't know yet
     *
     * @var bool|null
     */
    protected $usingUnnamedPlaceholders = null;

    /**
     * Gets the SQL statement as a string
     *
     * @return string The SQL statement
     */
    abstract public function getSQL();

    /**
     * Adds a named placeholder's value
     * Note that you cannot use a mix of named and unnamed placeholders in a query
     *
     * @param string $placeholderName The name of the placeholder (what comes after the ":")
     * @param mixed $value The value of the placeholder
     * @param int $dataType The PDO constant that indicates the type of data the value represents
     * @return $this
     * @throws InvalidQueryException Thrown if the user mixed unnamed placeholders with named placeholders
     */
    public function addNamedPlaceholderValue($placeholderName, $value, $dataType = PDO::PARAM_STR)
    {
        if($this->usingUnnamedPlaceholders === true)
        {
            throw new InvalidQueryException("Cannot mix unnamed placeholders with named placeholders");
        }

        $this->usingUnnamedPlaceholders = false;
        $this->parameters[$placeholderName] = [$value, $dataType];

        return $this;
    }

    /**
     * Adds named placeholders' values
     * Note that you cannot use a mix of named and unnamed placeholders in a query
     *
     * @param array $placeholderNamesToValues The mapping of placeholder names to their respective values
     *      Optionally, the names can map to an array whose first item is the value and whose second value is the
     *      PDO constant indicating the type of data the value represents
     * @return $this
     * @throws InvalidQueryException Thrown if the user mixed unnamed placeholders with named placeholders or
     *      if the value is an array that doesn't contain the correct number of items
     */
    public function addNamedPlaceholderValues(array $placeholderNamesToValues)
    {
        foreach($placeholderNamesToValues as $placeholderName => $value)
        {
            if(is_array($value))
            {
                if(count($value) != 2)
                {
                    throw new InvalidQueryException("Incorrect number of items in value array");
                }

                $this->addNamedPlaceholderValue($placeholderName, $value[0], $value[1]);
            }
            else
            {
                $this->addNamedPlaceholderValue($placeholderName, $value);
            }
        }

        return $this;
    }

    /**
     * Adds an unnamed placeholder's value
     * Note that you cannot use a mix of named and unnamed placeholders in a query
     *
     * @param mixed $value
     * @param int $dataType The PDO constant that indicates the type of data the value represents
     * @return $this
     * @throws InvalidQueryException Thrown if the user mixed unnamed placeholders with named placeholders
     */
    public function addUnnamedPlaceholderValue($value, $dataType = PDO::PARAM_STR)
    {
        if($this->usingUnnamedPlaceholders === false)
        {
            throw new InvalidQueryException("Cannot mix unnamed placeholders with named placeholders");
        }

        $this->usingUnnamedPlaceholders = true;
        $this->parameters[] = [$value, $dataType];

        return $this;
    }

    /**
     * Adds multiple unnamed placeholders' values
     * Note that you cannot use a mix of named and unnamed placeholders in a query
     *
     * @param array $placeholderValues The list of placeholder values
     *      Optionally, each value can be contained in an array whose first item is the value and whose second value is
     *      the PDO constant indicating the type of data the value represents
     * @return $this
     * @throws InvalidQueryException Thrown if the user mixed unnamed placeholders with named placeholders or
     *      if the value is an array that doesn't contain the correct number of items
     */
    public function addUnnamedPlaceholderValues(array $placeholderValues)
    {
        foreach($placeholderValues as $value)
        {
            if(is_array($value))
            {
                if(count($value) != 2)
                {
                    throw new InvalidQueryException("Incorrect number of items in value array");
                }

                $this->addUnnamedPlaceholderValue($value[0], $value[1]);
            }
            else
            {
                $this->addUnnamedPlaceholderValue($value);
            }
        }

        return $this;
    }

    /**
     * Gets the bound query parameters
     *
     * @return array The array of bound query parameters
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Removes a named placeholder from the query
     *
     * @param string $placeholderName The name of the placeholder to remove
     * @return $this
     * @throws InvalidQueryException Thrown if the user mixed unnamed placeholders with named placeholders
     */
    public function removeNamedPlaceHolder($placeholderName)
    {
        if($this->usingUnnamedPlaceholders === true)
        {
            throw new InvalidQueryException("Cannot mix unnamed placeholders with named placeholders");
        }

        unset($this->parameters[$placeholderName]);

        return $this;
    }

    /**
     * Removes an unnamed placeholder from the query
     *
     * @param int $placeholderIndex The index of the placeholder in the parameters to remove
     * @return $this
     * @throws InvalidQueryException Thrown if the user mixed unnamed placeholders with named placeholders
     */
    public function removeUnnamedPlaceHolder($placeholderIndex)
    {
        if($this->usingUnnamedPlaceholders === false)
        {
            throw new InvalidQueryException("Cannot mix unnamed placeholders with named placeholders");
        }

        unset($this->parameters[$placeholderIndex]);
        // Re-index the array
        $this->parameters = array_values($this->parameters);

        return $this;
    }

    /**
     * Sets the table we're querying
     *
     * @param string $tableName The name of the table we're querying
     * @param string $tableAlias The table alias
     */
    protected function setTable($tableName, $tableAlias = "")
    {
        $this->tableName = $tableName;
        $this->tableAlias = $tableAlias;
    }
} 