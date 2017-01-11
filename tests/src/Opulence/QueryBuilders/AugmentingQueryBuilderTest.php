<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\QueryBuilders;

/**
 * Tests the augmenting query builder
 */
class AugmentingQueryBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests adding multiple columns
     */
    public function testAddingMultipleColumns()
    {
        $queryBuilder = new AugmentingQueryBuilder();
        $queryBuilder->addColumnValues(['name' => 'dave']);
        $queryBuilder->addColumnValues(['email' => 'foo@bar.com']);
        $this->assertEquals(['name' => 'dave', 'email' => 'foo@bar.com'], $queryBuilder->getColumnNamesToValues());
    }

    /**
     * Tests adding a single column
     */
    public function testAddingSingleColumn()
    {
        $queryBuilder = new AugmentingQueryBuilder();
        $queryBuilder->addColumnValues(['name' => 'dave']);
        $this->assertEquals(['name' => 'dave'], $queryBuilder->getColumnNamesToValues());
    }
}
