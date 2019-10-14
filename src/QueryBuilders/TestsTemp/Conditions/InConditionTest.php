<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\QueryBuilders\TestsTemp\Conditions;

use InvalidArgumentException;
use Opulence\QueryBuilders\Conditions\InCondition;
use PDO;

/**
 * Tests the IN condition
 */
class InConditionTest extends \PHPUnit\Framework\TestCase
{
    public function testGettingParametersForInConditionWithParameters(): void
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
    public function testGettingParametersForInConditionWithSubExpression(): void
    {
        $condition = new InCondition('foo', 'SELECT bar FROM baz');
        $this->assertEquals([], $condition->getParameters());
    }

    public function testGettingSqlForInConditionWithParameters(): void
    {
        $condition = new InCondition('foo', [[1, PDO::PARAM_INT], [2, PDO::PARAM_INT], [3, PDO::PARAM_INT]]);
        $this->assertEquals('foo IN (?,?,?)', $condition->getSql());
    }

    /**
     * Tests getting SQL for IN condition with a sub-expression
     */
    public function testGettingSqlForInConditionWithSubExpression(): void
    {
        $condition = new InCondition('foo', 'SELECT bar FROM baz');
        $this->assertEquals('foo IN (SELECT bar FROM baz)', $condition->getSql());
    }

    public function testPassingInvalidArgumentThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new InCondition('foo', $this);
    }
}
