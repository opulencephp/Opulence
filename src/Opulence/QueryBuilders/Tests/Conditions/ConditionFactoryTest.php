<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\QueryBuilders\Tests\Conditions;

use Opulence\QueryBuilders\Conditions\ConditionFactory;
use Opulence\QueryBuilders\Conditions\BetweenCondition;
use Opulence\QueryBuilders\Conditions\InCondition;
use Opulence\QueryBuilders\Conditions\NotBetweenCondition;
use Opulence\QueryBuilders\Conditions\NotInCondition;

/**
 * Tests the condition factory
 */
class ConditionFactoryTest extends \PHPUnit\Framework\TestCase
{
    /** @var ConditionFactory The condition factory to use in tests */
    private $conditionFactory = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->conditionFactory = new ConditionFactory();
    }

    /**
     * Tests creating a BETWEEN condition
     */
    public function testCreatingBetweenCondition()
    {
        $this->assertInstanceOf(BetweenCondition::class, $this->conditionFactory->between('foo', 1, 2));
    }

    /**
     * Tests creating an IN condition
     */
    public function testCreatingInCondition()
    {
        $this->assertInstanceOf(InCondition::class, $this->conditionFactory->in('foo', [1, 2]));
    }

    /**
     * Tests creating a NOT BETWEEN condition
     */
    public function testCreatingNotBetweenCondition()
    {
        $this->assertInstanceOf(NotBetweenCondition::class, $this->conditionFactory->notBetween('foo', 1, 2));
    }

    /**
     * Tests creating a NOT IN condition
     */
    public function testCreatingNotInCondition()
    {
        $this->assertInstanceOf(NotInCondition::class, $this->conditionFactory->notIn('foo', [1, 2]));
    }
}
