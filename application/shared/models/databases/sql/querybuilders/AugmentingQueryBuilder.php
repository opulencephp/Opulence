<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Builds parts of a query that augment (INSERT/UPDATE)
 */
namespace RDev\Application\Shared\Models\Databases\SQL\QueryBuilders;

class AugmentingQueryBuilder
{
    /** @var array The mapping of column names to their respective values */
    protected $columnNamesToValues = array();

    /**
     * Adds column values to the query
     *
     * @param array $columnNamesToValues The mapping of column names to their respective values
     * @return $this
     */
    public function addColumnValues($columnNamesToValues)
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