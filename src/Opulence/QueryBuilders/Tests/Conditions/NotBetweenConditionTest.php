<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\QueryBuilders\Tests\Conditions;

use Opulence\QueryBuilders\Conditions\NotBetweenCondition;

/**
 * Tests the NOT BETWEEN condition
 */
class NotBetweenConditionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests getting the SQL
     */
    public function testGettingSql()
    {
        $condition = new NotBetweenCondition('foo', 1, 2);
        $this->assertEquals('foo NOT BETWEEN ? AND ?', $condition->getSql());
    }
}
