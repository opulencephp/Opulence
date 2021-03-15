<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Validation\Tests\Rules;

use InvalidArgumentException;
use LogicException;
use Opulence\Validation\Rules\MaxRule;

/**
 * Tests the max rule
 */
class MaxRuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests a failing rule
     */
    public function testFailingRule()
    {
        $rule = new MaxRule();
        $rule->setArgs([1.5]);
        $this->assertFalse($rule->passes(2));
        $this->assertFalse($rule->passes(1.6));
    }

    /**
     * Tests getting error placeholders
     */
    public function testGettingErrorPlaceholders()
    {
        $rule = new MaxRule();
        $rule->setArgs([2]);
        $this->assertEquals(['max' => 2], $rule->getErrorPlaceholders());
    }

    /**
     * Tests getting the slug
     */
    public function testGettingSlug()
    {
        $rule = new MaxRule();
        $this->assertEquals('max', $rule->getSlug());
    }

    /**
     * Tests not setting the args before passes
     */
    public function testNotSettingArgBeforePasses()
    {
        $this->expectException(LogicException::class);
        $rule = new MaxRule();
        $rule->passes(2);
    }

    /**
     * Tests passing an empty arg array
     */
    public function testPassingEmptyArgArray()
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new MaxRule();
        $rule->setArgs([]);
    }

    /**
     * Tests passing an invalid arg
     */
    public function testPassingInvalidArg()
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new MaxRule();
        $rule->setArgs([
            function () {
            }
        ]);
    }

    /**
     * Tests a passing value
     */
    public function testPassingValue()
    {
        $rule = new MaxRule();
        $rule->setArgs([2]);
        $this->assertTrue($rule->passes(2));
        $this->assertTrue($rule->passes(1));
        $this->assertTrue($rule->passes(1.5));
    }

    /**
     * Tests a value that is not inclusive
     */
    public function testValueThatIsNotInclusive()
    {
        $rule = new MaxRule();
        $rule->setArgs([2, false]);
        $this->assertFalse($rule->passes(2));
        $this->assertTrue($rule->passes(1.9));
    }
}
