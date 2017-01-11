<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\QueryBuilders\Conditions;

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
