<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\QueryBuilders\Tests\Conditions;

use Opulence\QueryBuilders\Conditions\NotInCondition;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Tests the NOT In condition
 */
class NotInConditionTest extends TestCase
{
    public function testGettingSqlForNotInConditionWithParameters(): void
    {
        $condition = new NotInCondition('foo', [[1, PDO::PARAM_INT], [2, PDO::PARAM_INT], [3, PDO::PARAM_INT]]);
        $this->assertEquals('foo NOT IN (?,?,?)', $condition->getSql());
    }

    /**
     * Tests getting SQL for NOT IN condition with a sub-expression
     */
    public function testGettingSqlForNotInConditionWithSubExpression(): void
    {
        $condition = new NotInCondition('foo', 'SELECT bar FROM baz');
        $this->assertEquals('foo NOT IN (SELECT bar FROM baz)', $condition->getSql());
    }
}
