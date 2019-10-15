<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\QueryBuilders\tests;

use Opulence\QueryBuilders\AugmentingQueryBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Tests the augmenting query builder
 */
class AugmentingQueryBuilderTest extends TestCase
{
    public function testAddingMultipleColumns(): void
    {
        $queryBuilder = new AugmentingQueryBuilder();
        $queryBuilder->addColumnValues(['name' => 'dave']);
        $queryBuilder->addColumnValues(['email' => 'foo@bar.com']);
        $this->assertEquals(['name' => 'dave', 'email' => 'foo@bar.com'], $queryBuilder->getColumnNamesToValues());
    }

    public function testAddingSingleColumn(): void
    {
        $queryBuilder = new AugmentingQueryBuilder();
        $queryBuilder->addColumnValues(['name' => 'dave']);
        $this->assertEquals(['name' => 'dave'], $queryBuilder->getColumnNamesToValues());
    }
}
