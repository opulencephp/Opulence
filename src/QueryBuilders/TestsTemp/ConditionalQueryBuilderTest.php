<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\QueryBuilders\TestsTemp;

use Opulence\QueryBuilders\ConditionalQueryBuilder;

/**
 * Tests the conditional query builder
 */
class ConditionalQueryBuilderTest extends \PHPUnit\Framework\TestCase
{
    public function testAddingConditionToEmptyClause(): void
    {
        $conditions = [];
        $queryBuilder = new ConditionalQueryBuilder();
        $conditions = $queryBuilder->addConditionToClause($conditions, 'AND', "name = 'dave'");
        $this->assertEquals([['operation' => 'AND', 'condition' => "name = 'dave'"]], $conditions);
    }

    /**
     * Tests adding a condition to a non-empty clause
     */
    public function testAddingConditionToNonEmptyClause(): void
    {
        $conditions = [['operation' => 'OR', 'condition' => "email = 'foo@bar.com'"]];
        $queryBuilder = new ConditionalQueryBuilder();
        $conditions = $queryBuilder->addConditionToClause($conditions, 'AND', "name = 'dave'");
        $this->assertEquals([
            ['operation' => 'OR', 'condition' => "email = 'foo@bar.com'"],
            ['operation' => 'AND', 'condition' => "name = 'dave'"]
        ], $conditions);
    }

    /**
     * Tests adding an "AND"ed "WHERE" statement
     */
    public function testAndWhere(): void
    {
        $queryBuilder = new ConditionalQueryBuilder();
        $queryBuilder->andWhere("name = 'dave'");
        $this->assertEquals(
            [['operation' => 'AND', 'condition' => "name = 'dave'"]],
            $queryBuilder->getWhereConditions()
        );
    }

    public function testGettingSql(): void
    {
        $queryBuilder = new ConditionalQueryBuilder();
        $queryBuilder->where("name = 'dave'")
            ->orWhere("email = 'foo@bar.com'")
            ->andWhere('awesome = true');
        $this->assertEquals(
            " WHERE (name = 'dave') OR (email = 'foo@bar.com') AND (awesome = true)",
            $queryBuilder->getClauseConditionSql('WHERE', $queryBuilder->getWhereConditions())
        );
    }

    /**
     * Tests adding an "OR"ed "WHERE" statement
     */
    public function testOrWhere(): void
    {
        $queryBuilder = new ConditionalQueryBuilder();
        $queryBuilder->orWhere("name = 'dave'");
        $this->assertEquals(
            [['operation' => 'OR', 'condition' => "name = 'dave'"]],
            $queryBuilder->getWhereConditions()
        );
    }

    /**
     * Tests adding "WHERE" statement
     */
    public function testWhere(): void
    {
        $queryBuilder = new ConditionalQueryBuilder();
        $queryBuilder->where("name = 'dave'");
        $this->assertEquals(
            [['operation' => 'AND', 'condition' => "name = 'dave'"]],
            $queryBuilder->getWhereConditions()
        );
    }
}
