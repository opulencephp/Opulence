<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\QueryBuilders\Tests\PostgreSql;

use Opulence\QueryBuilders\PostgreSql\AugmentingQueryBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Tests the augmenting query builder
 */
class AugmentingQueryBuilderTest extends TestCase
{
    /**
     * Tests adding to a "RETURNING" clause
     */
    public function testAddReturning(): void
    {
        $queryBuilder = new AugmentingQueryBuilder();
        $queryBuilder->returning('id')
            ->addReturning('name');
        $this->assertEquals(' RETURNING id, name', $queryBuilder->getReturningClauseSql());
    }

    /**
     * Tests adding a "RETURNING" clause
     */
    public function testReturning(): void
    {
        $queryBuilder = new AugmentingQueryBuilder();
        $queryBuilder->returning('id');
        $this->assertEquals(' RETURNING id', $queryBuilder->getReturningClauseSql());
    }
}
