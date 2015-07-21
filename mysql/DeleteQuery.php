<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Builds a delete query
 */
namespace Opulence\QueryBuilders\MySQL;
use Opulence\QueryBuilders\DeleteQuery as BaseDeleteQuery;

class DeleteQuery extends BaseDeleteQuery
{
    /** @var int|string $limit The number of rows to limit to */
    protected $limit = -1;

    /**
     * @inheritdoc
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
     * @param int|string $numRows The number of rows to limit in the results
     *      or the named placeholder value that will contain the number of rows
     * @return $this
     */
    public function limit($numRows)
    {
        $this->limit = $numRows;

        return $this;
    }
} 