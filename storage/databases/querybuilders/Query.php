<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 *
 *
 * Defines common functionality for query classes
 */
namespace RamODev\Storage\Databases\QueryBuilders;
use RamODev\Storage\Databases\QueryBuilders\Exceptions;

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