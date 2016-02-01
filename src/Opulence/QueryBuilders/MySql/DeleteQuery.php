<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\QueryBuilders\MySql;

use Opulence\QueryBuilders\DeleteQuery as BaseDeleteQuery;

/**
 * Builds a delete query
 */
class DeleteQuery extends BaseDeleteQuery
{
    /** @var int|string $limit The number of rows to limit to */
    protected $limit = -1;

    /**
     * @inheritdoc
     */
    public function getSql() : string
    {
        $sql = parent::getSql();

        // Add a limit
        if ($this->limit !== -1) {
            $sql .= " LIMIT {$this->limit}";
        }

        return $sql;
    }

    /**
     * Limits the number of rows returned by the query
     *
     * @param int|string $numRows The number of rows to limit in the results
     *      or the named placeholder value that will contain the number of rows
     * @return self For method chaining
     */
    public function limit($numRows) : self
    {
        $this->limit = $numRows;

        return $this;
    }
} 