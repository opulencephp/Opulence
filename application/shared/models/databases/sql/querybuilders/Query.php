<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines common functionality for query classes
 */
namespace RamODev\Application\Shared\Models\Databases\SQL\QueryBuilders;
use RamODev\Application\Shared\Models\Databases\SQL\QueryBuilders\Exceptions;

abstract class Query
{
    /** @var string The name of the table we're querying */
    protected $tableName = "";
    /** @var string The alias of the table we're querying */
    protected $tableAlias = "";
    /** @var array The list of bound parameters */
    protected $parameters = array();
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
     * @return $this
     * @throws Exceptions\InvalidQueryException Thrown if the user mixed unnamed placeholders with named placeholders
     */
    public function addNamedPlaceholderValue($placeholderName, $value)
    {
        if($this->usingUnnamedPlaceholders === true)
        {
            throw new Exceptions\InvalidQueryException("Cannot mix unnamed placeholders with named placeholders");
        }

        $this->usingUnnamedPlaceholders = false;
        $this->parameters[$placeholderName] = $value;

        return $this;
    }

    /**
     * Adds named placeholders' values
     * Note that you cannot use a mix of named and unnamed placeholders in a query
     *
     * @param array $placeholderNamesToValues The mapping of placeholder names to their respective values
     * @return $this
     * @throws Exceptions\InvalidQueryException Thrown if the user mixed unnamed placeholders with named placeholders
     */
    public function addNamedPlaceholderValues($placeholderNamesToValues)
    {
        foreach($placeholderNamesToValues as $placeholderName => $value)
        {
            $this->addNamedPlaceholderValue($placeholderName, $value);
        }

        return $this;
    }

    /**
     * Adds an unnamed placeholder's value
     * Note that you cannot use a mix of named and unnamed placeholders in a query
     *
     * @param mixed $value
     * @return $this
     * @throws Exceptions\InvalidQueryException Thrown if the user mixed unnamed placeholders with named placeholders
     */
    public function addUnnamedPlaceholderValue($value)
    {
        if($this->usingUnnamedPlaceholders === false)
        {
            throw new Exceptions\InvalidQueryException("Cannot mix unnamed placeholders with named placeholders");
        }

        $this->usingUnnamedPlaceholders = true;
        $this->parameters[] = $value;

        return $this;
    }

    /**
     * Adds multiple unnamed placeholders' values
     * Note that you cannot use a mix of named and unnamed placeholders in a query
     *
     * @param array $placeholderValues The list of placeholder values
     * @return $this
     * @throws Exceptions\InvalidQueryException Thrown if the user mixed unnamed placeholders with named placeholders
     */
    public function addUnnamedPlaceholderValues($placeholderValues)
    {
        foreach($placeholderValues as $value)
        {
            $this->addUnnamedPlaceholderValue($value);
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
     * @throws Exceptions\InvalidQueryException Thrown if the user mixed unnamed placeholders with named placeholders
     */
    public function removeNamedPlaceHolder($placeholderName)
    {
        if($this->usingUnnamedPlaceholders === true)
        {
            throw new Exceptions\InvalidQueryException("Cannot mix unnamed placeholders with named placeholders");
        }

        unset($this->parameters[$placeholderName]);

        return $this;
    }

    /**
     * Removes an unnamed placeholder from the query
     *
     * @param int $placeholderIndex The index of the placeholder in the parameters to remove
     * @return $this
     * @throws Exceptions\InvalidQueryException Thrown if the user mixed unnamed placeholders with named placeholders
     */
    public function removeUnnamedPlaceHolder($placeholderIndex)
    {
        if($this->usingUnnamedPlaceholders === false)
        {
            throw new Exceptions\InvalidQueryException("Cannot mix unnamed placeholders with named placeholders");
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