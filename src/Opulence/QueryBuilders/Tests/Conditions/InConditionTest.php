<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\QueryBuilders\Tests\Conditions;

use InvalidArgumentException;
use Opulence\QueryBuilders\Conditions\InCondition;
use PDO;

/**
 * Tests the IN condition
 */
class InConditionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests getting parameters for IN condition with parameters
     */
    public function testGettingParametersForInConditionWithParameters()
    {
        $condition = new InCondition('foo', [[1, PDO::PARAM_INT], [2, PDO::PARAM_INT], [3, PDO::PARAM_INT]]);
        $this->assertEquals(
            [[1, PDO::PARAM_INT], [2, PDO::PARAM_INT], [3, PDO::PARAM_INT]],
            $condition->getParameters()
        );
    }

    /**
     * Tests getting parameters for IN condition with a sub-expression
     */
    public function testGettingParametersForInConditionWithSubExpression()
    {
        $condition = new InCondition('foo', 'SELECT bar FROM baz');
        $this->assertEquals([], $condition->getParameters());
    }

    /**
     * Tests getting SQL for IN condition with parameters
     */
    public function testGettingSqlForInConditionWithParameters()
    {
        $condition = new InCondition('foo', [[1, PDO::PARAM_INT], [2, PDO::PARAM_INT], [3, PDO::PARAM_INT]]);
        $this->assertEquals('foo IN (?,?,?)', $condition->getSql());
    }

    /**
     * Tests getting SQL for IN condition with a sub-expression
     */
    public function testGettingSqlForInConditionWithSubExpression()
    {
        $condition = new InCondition('foo', 'SELECT bar FROM baz');
        $this->assertEquals('foo IN (SELECT bar FROM baz)', $condition->getSql());
    }

    /**
     * Tests passing an invalid argument throws an exception
     */
    public function testPassingInvalidArgumentThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        new InCondition('foo', $this);
    }
}
