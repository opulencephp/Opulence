<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\QueryBuilders\Tests\MySql;

use Opulence\QueryBuilders\MySql\DeleteQuery;
use Opulence\QueryBuilders\MySql\InsertQuery;
use Opulence\QueryBuilders\MySql\QueryBuilder;
use Opulence\QueryBuilders\MySql\SelectQuery;
use Opulence\QueryBuilders\MySql\UpdateQuery;

/**
 * Tests the query builder
 */
class QueryBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that the query builder returns a DeleteQuery when we call delete()
     */
    public function testThatDeleteReturnsDeleteQueryBuilder(): void
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf(
            DeleteQuery::class,
            $queryBuilder->delete('tableName', 'tableAlias')
        );
    }

    /**
     * Tests that the query builder returns a InsertQuery when we call insert()
     */
    public function testThatInsertReturnsInsertQueryBuilder(): void
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf(
            InsertQuery::class,
            $queryBuilder->insert('tableName', ['columnName' => 'columnValue'])
        );
    }

    /**
     * Tests that the query builder returns a SelectQuery when we call select()
     */
    public function testThatSelectReturnsSelectQueryBuilder(): void
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf(
            SelectQuery::class,
            $queryBuilder->select('tableName', 'tableAlias')
        );
    }

    /**
     * Tests that the query builder returns a UpdateQuery when we call update()
     */
    public function testThatUpdateReturnsUpdateQueryBuilder(): void
    {
        $queryBuilder = new QueryBuilder();
        $this->assertInstanceOf(
            UpdateQuery::class,
            $queryBuilder->update('tableName', 'tableAlias', ['columnName' => 'columnValue'])
        );
    }
}
