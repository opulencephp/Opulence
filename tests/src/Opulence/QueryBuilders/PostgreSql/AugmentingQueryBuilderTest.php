<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\QueryBuilders\PostgreSql;

/**
 * Tests the augmenting query builder
 */
class AugmentingQueryBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests adding to a "RETURNING" clause
     */
    public function testAddReturning()
    {
        $queryBuilder = new AugmentingQueryBuilder();
        $queryBuilder->returning('id')
            ->addReturning('name');
        $this->assertEquals(' RETURNING id, name', $queryBuilder->getReturningClauseSql());
    }

    /**
     * Tests adding a "RETURNING" clause
     */
    public function testReturning()
    {
        $queryBuilder = new AugmentingQueryBuilder();
        $queryBuilder->returning('id');
        $this->assertEquals(' RETURNING id', $queryBuilder->getReturningClauseSql());
    }
}
