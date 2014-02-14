<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Builds a delete query
 */
namespace RamODev\Databases\RDBMS\MySQL\QueryBuilders;
use RamODev\Databases\RDBMS\QueryBuilders;

require_once(__DIR__ . "/../../querybuilders/DeleteQuery.php");

class DeleteQuery extends QueryBuilders\DeleteQuery
{
    /** @var int $limit The number of rows to limit to */
    protected $limit = -1;

    /**
     * Gets the SQL statement as a string
     *
     * @return string The SQL statement
     */
    public function getSQL()
    {
        $sql = parent::getSQL();

        // Add a limit
        if($this->limit !== -1)
        {
            $sql .= " LIMIT " . $this->limit;
        }

        return $sql;
    }

    /**
     * Limits the number of rows returned by the query
     *
     * @param int $numRows The number of rows to limit in the results
     * @return $this
     */
    public function limit($numRows)
    {
        $this->limit = (int)$numRows;

        return $this;
    }
} 