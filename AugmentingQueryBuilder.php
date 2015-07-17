<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Builds parts of a query that augment (INSERT/UPDATE)
 */
namespace Opulence\QueryBuilders;

class AugmentingQueryBuilder
{
    /** @var array The mapping of column names to their respective values */
    protected $columnNamesToValues = [];

    /**
     * Adds column values to the query
     *
     * @param array $columnNamesToValues The mapping of column names to their respective values
     * @return $this
     */
    public function addColumnValues(array $columnNamesToValues)
    {
        $this->columnNamesToValues = array_merge($this->columnNamesToValues, $columnNamesToValues);

        return $this;
    }

    /**
     * @return array
     */
    public function getColumnNamesToValues()
    {
        return $this->columnNamesToValues;
    }
} 