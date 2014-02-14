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
namespace RamODev\Databases\RDBMS\MySQL\QueryBuilders;
use RamODev\Databases\RDBMS\QueryBuilders;

require_once(__DIR__ . "/../../querybuilders/InsertQuery.php");

class InsertQuery extends QueryBuilders\InsertQuery
{
    /** @var array The list of column names to values in case of a "ON DUPLICATE KEY UPDATE" clause */
    private $duplicateKeyUpdateColumnNamesToValues = array();

    /**
     * Adds columns to update in the case a row already exists in the table
     *
     * @param array $columnNamesToValues The mapping of column names to their respective values in the case of an "ON DUPLICATE KEY UPDATE" clause
     * @return $this
     */
    public function addUpdateColumnValues($columnNamesToValues)
    {
        $this->duplicateKeyUpdateColumnNamesToValues = array_merge($this->duplicateKeyUpdateColumnNamesToValues, $columnNamesToValues);

        return $this;
    }

    /**
     * Gets the SQL statement as a string
     *
     * @return string The SQL statement
     */
    public function getSQL()
    {
        $sql = parent::getSQL();

        if(count($this->duplicateKeyUpdateColumnNamesToValues) > 0)
        {
            $sql .= " ON DUPLICATE KEY UPDATE";

            foreach($this->duplicateKeyUpdateColumnNamesToValues as $columnName => $value)
            {
                $sql .= " " . $columnName . " = ?,";
            }

            $sql = trim($sql, ",");
        }

        return $sql;
    }

    /**
     * Allows a user to "UPDATE" rather than "INSERT" in the case a row already exists in the table
     * Only call this method once per query because it will overwrite an previously-set "ON DUPLICATE KEY UPDATE" expressions
     *
     * @param array $columnNamesToValues The mapping of column names to their respective values in the case of an "ON DUPLICATE KEY UPDATE" clause
     * @return $this
     */
    public function update($columnNamesToValues)
    {
        $this->duplicateKeyUpdateColumnNamesToValues = $columnNamesToValues;

        return $this;
    }
} 