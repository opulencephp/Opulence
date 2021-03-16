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
use Opulence\Validation\Rules\ConditionalRule;
use Opulence\Validation\Rules\IRule;

/**
 * Tests the conditional rule
 */
class ConditionalRuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that it fails when the condition is true and an extra rule fails
     */
    public function TestFailsWhenConditionTrueAndExtraRuleFails()
    {
        $callback = function () {
            return true;
        };
        $rule = new ConditionalRule();
        $rule->setArgs([$callback]);
        /** @var IRule|\PHPUnit_Framework_MockObject_MockObject $extraRule */
        $extraRule = $this->createMock(IRule::class);
        $extraRule->expects($this->once())
            ->method('passes')
            ->willReturn(false);
        $rule->addRule($extraRule);
        $this->assertFalse($rule->passes('foo'));
    }

    /**
     * Tests getting the slug
     */
    public function testGettingSlug()
    {
        $rule = new ConditionalRule();
        $this->assertEquals('conditional', $rule->getSlug());
    }

    /**
     * Tests getting sub-rules
     */
    public function testGettingSubRules()
    {
        /** @var IRule|\PHPUnit_Framework_MockObject_MockObject $subRule1 */
        $subRule1 = $this->createMock(IRule::class);
        /** @var IRule|\PHPUnit_Framework_MockObject_MockObject $subRule2 */
        $subRule2 = $this->createMock(IRule::class);
        $rule = new ConditionalRule();
        $rule->addRule($subRule1);
        $rule->addRule($subRule2);
        $this->assertEquals([$subRule1, $subRule2], $rule->getRules());
    }

    /**
     * Tests not setting the args before passes
     */
    public function testNotSettingArgBeforePasses()
    {
        $this->expectException(LogicException::class);
        $rule = new ConditionalRule();
        $rule->passes('foo');
    }

    /**
     * Tests that it a rule always passes with no extra rules
     */
    public function testPassesWithNoExtraRules()
    {
        $trueCallback = function () {
            return true;
        };
        $falseCallback = function () {
            return false;
        };
        $trueRule = new ConditionalRule();
        $falseRule = new ConditionalRule();
        $trueRule->setArgs([$trueCallback]);
        $falseRule->setArgs([$falseCallback]);
        $this->assertTrue($trueRule->passes('foo'));
        $this->assertTrue($falseRule->passes('foo'));
    }

    /**
     * Tests passing an empty arg array
     */
    public function testPassingEmptyArgArray()
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new ConditionalRule();
        $rule->setArgs([]);
    }

    /**
     * Tests passing an invalid arg
     */
    public function testPassingInvalidArg()
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new ConditionalRule();
        $rule->setArgs(['foo']);
    }

    /**
     * Tests that rules always pass when the condition returns false
     */
    public function testRulesAwaysPassWhenConditionReturnsFalse()
    {
        $callback = function () {
            return false;
        };
        $rule = new ConditionalRule();
        $rule->setArgs([$callback]);
        /** @var IRule|\PHPUnit_Framework_MockObject_MockObject $extraRule */
        $extraRule = $this->createMock(IRule::class);
        $extraRule->expects($this->any())
            ->method('passes')
            ->willReturn(false);
        $rule->addRule($extraRule);
        $this->assertTrue($rule->passes('foo'));
    }
}
