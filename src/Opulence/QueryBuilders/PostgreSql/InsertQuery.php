<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\QueryBuilders\PostgreSql;

use Opulence\QueryBuilders\InsertQuery as BaseInsertQuery;

/**
 * Builds an insert query
 */
class InsertQuery extends BaseInsertQuery
{
    /** @var AugmentingQueryBuilder Handles functionality common to augmenting queries */
    protected $augmentingQueryBuilder = null;

    /**
     * @param string $tableName The name of the table we're inserting into
     * @param array $columnNamesToValues The mapping of column names to their respective values
     */
    public function __construct($tableName, array $columnNamesToValues)
    {
        parent::__construct($tableName, $columnNamesToValues);

        $this->augmentingQueryBuilder = new AugmentingQueryBuilder();
        $this->augmentingQueryBuilder->addColumnValues($columnNamesToValues);
    }

    /**
     * Adds to a "RETURNING" clause
     *
     * @param string $expression,... A variable list of expressions to add to the "RETURNING" clause
     * @return $this
     */
    public function addReturning($expression)
    {
        call_user_func_array([$this->augmentingQueryBuilder, "addReturning"], func_get_args());

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSql()
    {
        $sql = parent::getSql();
        $sql .= $this->augmentingQueryBuilder->getReturningClauseSql();

        return $sql;
    }

    /**
     * Starts a "RETURNING" clause
     * Only call this method once per query because it will overwrite any previously-set "RETURNING" expressions
     *
     * @param string $expression,... A variable list of expressions to add to the "RETURNING" clause
     * @return $this
     */
    public function returning($expression)
    {
        call_user_func_array([$this->augmentingQueryBuilder, "returning"], func_get_args());

        return $this;
    }
} 