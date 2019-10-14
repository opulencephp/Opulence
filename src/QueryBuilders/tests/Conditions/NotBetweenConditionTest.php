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

use Opulence\QueryBuilders\Conditions\NotBetweenCondition;

/**
 * Tests the NOT BETWEEN condition
 */
class NotBetweenConditionTest extends \PHPUnit\Framework\TestCase
{
    public function testGettingSql(): void
    {
        $condition = new NotBetweenCondition('foo', 1, 2);
        $this->assertEquals('foo NOT BETWEEN ? AND ?', $condition->getSql());
    }
}
