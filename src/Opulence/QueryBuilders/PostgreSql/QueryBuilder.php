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

use Opulence\QueryBuilders\QueryBuilder as BaseQueryBuilder;
use ReflectionClass;

/**
 * Programmatically builds up a PostgreSQL query
 */
class QueryBuilder extends BaseQueryBuilder
{
    /**
     * @inheritdoc
     */
    public function delete(string $tableName, string $alias = ''): DeleteQuery
    {
        return new DeleteQuery($tableName, $alias);
    }

    /**
     * @inheritdoc
     */
    public function insert(string $tableName, array $columnNamesToValues): InsertQuery
    {
        return new InsertQuery($tableName, $columnNamesToValues);
    }

    /**
     * @inheritdoc
     * @return SelectQuery
     */
    public function select(...$expression): SelectQuery
    {
        // This code allows us to pass a variable list of parameters to a class constructor
        $queryClass = new ReflectionClass(SelectQuery::class);

        return $queryClass->newInstanceArgs($expression);
    }

    /**
     * @inheritdoc
     */
    public function update(string $tableName, string $alias, array $columnNamesToValues): UpdateQuery
    {
        return new UpdateQuery($tableName, $alias, $columnNamesToValues);
    }
}
