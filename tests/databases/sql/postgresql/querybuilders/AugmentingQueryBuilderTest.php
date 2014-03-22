<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the augmenting query builder
 */
namespace RamODev\Application\Databases\SQL\PostgreSQL\QueryBuilders;

class AugmentingQueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding to a "RETURNING" clause
     */
    public function testAddReturning()
    {
        $queryBuilder = new AugmentingQueryBuilder();
        $queryBuilder->returning("id")
            ->addReturning("name");
        $this->assertEquals(" RETURNING id, name", $queryBuilder->getReturningClauseSQL());
    }

    /**
     * Tests adding a "RETURNING" clause
     */
    public function testReturning()
    {
        $queryBuilder = new AugmentingQueryBuilder();
        $queryBuilder->returning("id");
        $this->assertEquals(" RETURNING id", $queryBuilder->getReturningClauseSQL());
    }
} 