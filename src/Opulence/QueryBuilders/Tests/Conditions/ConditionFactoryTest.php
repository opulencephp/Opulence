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
use Opulence\QueryBuilders\Conditions\ConditionFactory;
use Opulence\QueryBuilders\Conditions\InCondition;
use Opulence\QueryBuilders\Conditions\NotBetweenCondition;
use Opulence\QueryBuilders\Conditions\NotInCondition;

/**
 * Tests the condition factory
 */
class ConditionFactoryTest extends \PHPUnit\Framework\TestCase
{
    private ConditionFactory $conditionFactory;

    protected function setUp(): void
    {
        $this->conditionFactory = new ConditionFactory();
    }

    public function testCreatingBetweenCondition(): void
    {
        $this->assertInstanceOf(BetweenCondition::class, $this->conditionFactory->between('foo', 1, 2));
    }

    public function testCreatingInCondition(): void
    {
        $this->assertInstanceOf(InCondition::class, $this->conditionFactory->in('foo', [1, 2]));
    }

    public function testCreatingNotBetweenCondition(): void
    {
        $this->assertInstanceOf(NotBetweenCondition::class, $this->conditionFactory->notBetween('foo', 1, 2));
    }

    public function testCreatingNotInCondition(): void
    {
        $this->assertInstanceOf(NotInCondition::class, $this->conditionFactory->notIn('foo', [1, 2]));
    }
}
