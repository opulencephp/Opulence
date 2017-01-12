<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\QueryBuilders\Conditions;

use PDO;

/**
 * Tests the NOT In condition
 */
class NotInConditionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests getting SQL for NOT IN condition with parameters
     */
    public function testGettingSqlForNotInConditionWithParameters()
    {
        $condition = new NotInCondition('foo', [[1, PDO::PARAM_INT], [2, PDO::PARAM_INT], [3, PDO::PARAM_INT]]);
        $this->assertEquals('foo NOT IN (?,?,?)', $condition->getSql());
    }

    /**
     * Tests getting SQL for NOT IN condition with a sub-expression
     */
    public function testGettingSqlForNotInConditionWithSubExpression()
    {
        $condition = new NotInCondition('foo', 'SELECT bar FROM baz');
        $this->assertEquals('foo NOT IN (SELECT bar FROM baz)', $condition->getSql());
    }
}
