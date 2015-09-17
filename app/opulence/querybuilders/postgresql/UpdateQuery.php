<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Builds an update query
 */
namespace Opulence\QueryBuilders\PostgreSQL;
use Opulence\QueryBuilders\UpdateQuery as BaseUpdateQuery;

class UpdateQuery extends BaseUpdateQuery
{
    /** @var AugmentingQueryBuilder Handles functionality common to augmenting queries */
    protected $augmentingQueryBuilder = null;

    /**
     * @param string $tableName The name of the table we're querying
     * @param string $tableAlias The alias of the table we're querying
     * @param array $columnNamesToValues The mapping of column names to their respective values
     */
    public function __construct($tableName, $tableAlias, array $columnNamesToValues)
    {
        parent::__construct($tableName, $tableAlias, $columnNamesToValues);

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
    public function getSQL()
    {
        $sql = parent::getSQL();
        $sql .= $this->augmentingQueryBuilder->getReturningClauseSQL();

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