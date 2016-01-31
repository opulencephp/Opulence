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
 * Builds parts of a query that augment (INSERT/UPDATE)
 */
class AugmentingQueryBuilder
{
    /** @var array The mapping of column names to their respective values */
    protected $columnNamesToValues = [];

    /**
     * Adds column values to the query
     *
     * @param array $columnNamesToValues The mapping of column names to their respective values
     * @return self For method chaining
     */
    public function addColumnValues(array $columnNamesToValues) : self
    {
        $this->columnNamesToValues = array_merge($this->columnNamesToValues, $columnNamesToValues);

        return $this;
    }

    /**
     * @return array
     */
    public function getColumnNamesToValues() : array
    {
        return $this->columnNamesToValues;
    }
} 