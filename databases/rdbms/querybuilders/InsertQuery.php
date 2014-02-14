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
 * Builds an insert query
 */
namespace RamODev\Databases\RDBMS\QueryBuilders;

require_once(__DIR__ . "/Query.php");
require_once(__DIR__ . "/AugmentingQueryBuilder.php");

class InsertQuery extends Query
{
    /** @var AugmentingQueryBuilder Handles functionality common to augmenting queries */
    protected $augmentingQueryBuilder = null;

    /**
     * @param string $tableName The name of the table we're inserting into
     * @param array $columnNamesToValues The mapping of column names to their respective values
     */
    public function __construct($tableName, $columnNamesToValues)
    {
        $this->tableName = $tableName;
        $this->augmentingQueryBuilder = new AugmentingQueryBuilder();
        $this->addColumnValues($columnNamesToValues);
    }

    /**
     * Adds column values to our query
     *
     * @param array $columnNamesToValues The mapping of column names to their respective values
     * @return $this
     */
    public function addColumnValues($columnNamesToValues)
    {
        $this->addUnnamedPlaceholderValues(array_values($columnNamesToValues));
        $this->augmentingQueryBuilder->addColumnValues($columnNamesToValues);

        return $this;
    }

    /**
     * Gets the SQL statement as a string
     *
     * @return string The SQL statement
     */
    public function getSQL()
    {
        $sql = "INSERT INTO " . $this->tableName . " (" . implode(", ", array_keys($this->augmentingQueryBuilder->getColumnNamesToValues())) . ") VALUES (" . implode(", ", array_fill(0, count(array_values($this->augmentingQueryBuilder->getColumnNamesToValues())), "?")) . ")";

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