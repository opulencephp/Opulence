<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\QueryBuilders\TestsTemp\PostgreSql;

use Opulence\QueryBuilders\PostgreSql\DeleteQuery;
use Opulence\QueryBuilders\PostgreSql\InsertQuery;
use Opulence\QueryBuilders\PostgreSql\QueryBuilder;
use Opulence\QueryBuilders\PostgreSql\SelectQuery;
use Opulence\QueryBuilders\PostgreSql\UpdateQuery;

/**
 * Tests the query builder
 */
class QueryBuilderTest extends \PHPUnit\Framework\TestCase
{
    public function testThatDeleteReturnsDeleteQueryBuilder(): void
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf(
            DeleteQuery::class,
            $queryBuilder->delete('tableName', 'tableAlias')
        );
    }

    public function testThatInsertReturnsInsertQueryBuilder(): void
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf(
            InsertQuery::class,
            $queryBuilder->insert('tableName', ['columnName' => 'columnValue'])
        );
    }

    public function testThatSelectReturnsSelectQueryBuilder(): void
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf(
            SelectQuery::class,
            $queryBuilder->select('tableName', 'tableAlias')
        );
    }

    public function testThatUpdateReturnsUpdateQueryBuilder(): void
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf(
            UpdateQuery::class,
            $queryBuilder->update('tableName', 'tableAlias', ['columnName' => 'columnValue'])
        );
    }
}
