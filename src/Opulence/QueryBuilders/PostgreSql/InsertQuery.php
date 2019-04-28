<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\QueryBuilders\PostgreSql;

use Opulence\QueryBuilders\InsertQuery as BaseInsertQuery;

/**
 * Builds an insert query
 */
class InsertQuery extends BaseInsertQuery
{
    /** @var AugmentingQueryBuilder Handles functionality common to augmenting queries */
    protected $augmentingQueryBuilder;

    /**
     * @param string $tableName The name of the table we're inserting into
     * @param array $columnNamesToValues The mapping of column names to their respective values
     */
    public function __construct(string $tableName, array $columnNamesToValues)
    {
        parent::__construct($tableName, $columnNamesToValues);

        $this->augmentingQueryBuilder = new AugmentingQueryBuilder();
        $this->augmentingQueryBuilder->addColumnValues($columnNamesToValues);
    }

    /**
     * Adds to a "RETURNING" clause
     *
     * @param string[] $expression,... A variable list of expressions to add to the "RETURNING" clause
     * @return self For method chaining
     */
    public function addReturning(string ...$expression): self
    {
        $this->augmentingQueryBuilder->addReturning(...$expression);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSql(): string
    {
        $sql = parent::getSql();
        $sql .= $this->augmentingQueryBuilder->getReturningClauseSql();

        return $sql;
    }

    /**
     * Starts a "RETURNING" clause
     * Only call this method once per query because it will overwrite any previously-set "RETURNING" expressions
     *
     * @param string[] $expression,... A variable list of expressions to add to the "RETURNING" clause
     * @return self For method chaining
     */
    public function returning(string ...$expression): self
    {
        $this->augmentingQueryBuilder->returning(...$expression);

        return $this;
    }
}
