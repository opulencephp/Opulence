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

use Opulence\QueryBuilders\Conditions\BetweenCondition;
use PDO;

/**
 * Tests the BETWEEN condition
 */
class BetweenConditionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests getting parameters for BETWEEN condition with a data type
     */
    public function testGettingParametersWithDataType(): void
    {
        $condition = new BetweenCondition('foo', 1, 2, PDO::PARAM_INT);
        $this->assertEquals(
            [[1, PDO::PARAM_INT], [2, PDO::PARAM_INT]],
            $condition->getParameters()
        );
    }

    /**
     * Tests getting parameters for BETWEEN condition with no data type
     */
    public function testGettingParametersWithNoDataType(): void
    {
        $condition = new BetweenCondition('foo', 1, 2);
        $this->assertEquals(
            [[1, PDO::PARAM_STR], [2, PDO::PARAM_STR]],
            $condition->getParameters()
        );
    }

    /**
     * Tests getting the SQL
     */
    public function testGettingSql(): void
    {
        $condition = new BetweenCondition('foo', 1, 2);
        $this->assertEquals('foo BETWEEN ? AND ?', $condition->getSql());
    }
}
