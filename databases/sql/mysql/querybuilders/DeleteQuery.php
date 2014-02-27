<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Builds a delete query
 */
namespace RamODev\Databases\SQL\MySQL\QueryBuilders;
use RamODev\Databases\SQL\QueryBuilders;

require_once(__DIR__ . "/../../querybuilders/DeleteQuery.php");

class DeleteQuery extends QueryBuilders\DeleteQuery
{
    /** @var int|string $limit The number of rows to limit to */
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
     * @param int|string $numRows The number of rows to limit in the results or the named placeholder value that will contain the number of rows
     * @return $this
     */
    public function limit($numRows)
    {
        $this->limit = $numRows;

        return $this;
    }
} 