<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\QueryBuilders\MySql;

use Opulence\QueryBuilders\InsertQuery as BaseInsertQuery;

/**
 * Builds an insert query
 */
class InsertQuery extends BaseInsertQuery
{
    /** @var array The list of column names to values in case of a "ON DUPLICATE KEY UPDATE" clause */
    private $duplicateKeyUpdateColumnNamesToValues = [];

    /**
     * Adds columns to update in the case a row already exists in the table
     *
     * @param array $columnNamesToValues The mapping of column names to their respective values in the case of an
     *      "ON DUPLICATE KEY UPDATE" clause
     * @return self For method chaining
     */
    public function addUpdateColumnValues(array $columnNamesToValues) : self
    {
        $this->duplicateKeyUpdateColumnNamesToValues = array_merge(
            $this->duplicateKeyUpdateColumnNamesToValues,
            $columnNamesToValues
        );

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSql() : string
    {
        $sql = parent::getSql();

        // Add a potential "UPDATE"
        if (count($this->duplicateKeyUpdateColumnNamesToValues) > 0) {
            $sql .= " ON DUPLICATE KEY UPDATE";

            foreach ($this->duplicateKeyUpdateColumnNamesToValues as $columnName => $value) {
                $sql .= " $columnName = ?,";
            }

            $sql = trim($sql, ",");
        }

        return $sql;
    }

    /**
     * Allows a user to "UPDATE" rather than "INSERT" in the case a row already exists in the table
     * Only call this method once per query because it will overwrite any previously-set "ON DUPLICATE KEY UPDATE" expressions
     *
     * @param array $columnNamesToValues The mapping of column names to their respective values in the case of an
     *      "ON DUPLICATE KEY UPDATE" clause
     * @return self For method chaining
     */
    public function update(array $columnNamesToValues) : self
    {
        $this->duplicateKeyUpdateColumnNamesToValues = $columnNamesToValues;

        return $this;
    }
} 