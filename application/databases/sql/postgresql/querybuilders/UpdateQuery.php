<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Builds an update query
 */
namespace RamODev\Application\Databases\SQL\PostgreSQL\QueryBuilders;
use RamODev\Application\Databases\SQL\QueryBuilders;

class UpdateQuery extends QueryBuilders\UpdateQuery
{
    /** @var AugmentingQueryBuilder Handles functionality common to augmenting queries */
    protected $augmentingQueryBuilder = null;

    /**
     * @param string $tableName The name of the table we're querying
     * @param string $tableAlias The alias of the table we're querying
     * @param array $columnNamesToValues The mapping of column names to their respective values
     */
    public function __construct($tableName, $tableAlias, $columnNamesToValues)
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
        call_user_func_array(array($this->augmentingQueryBuilder, "addReturning"), func_get_args());

        return $this;
    }

    /**
     * Gets the SQL statement as a string
     *
     * @return string The SQL statement
     */
    public function getSQL()
    {
        $sql = parent::getSQL();
        $sql .= $this->augmentingQueryBuilder->getReturningClauseSQL();

        return $sql;
    }

    /**
     * Starts a "RETURNING" clause
     * Only call this method once per query because it will overwrite an previously-set "RETURNING" expressions
     *
     * @param string $expression,... A variable list of expressions to add to the "RETURNING" clause
     * @return $this
     */
    public function returning($expression)
    {
        call_user_func_array(array($this->augmentingQueryBuilder, "returning"), func_get_args());

        return $this;
    }
} 