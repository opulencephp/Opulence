<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation\Rules;

/**
 * Tests the conditional rule
 */
class ConditionalRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that it fails when the condition is true and an extra rule fails
     */
    public function TestFailsWhenConditionTrueAndExtraRuleFails()
    {
        $callback = function () {
            return true;
        };
        $rule = new ConditionalRule($callback);
        /** @var IRule|\PHPUnit_Framework_MockObject_MockObject $extraRule */
        $extraRule = $this->getMock(IRule::class);
        $extraRule->expects($this->once())
            ->method("passes")
            ->willReturn(false);
        $rule->addRule($extraRule);
        $this->assertFalse($rule->passes("foo"));
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
        $trueRule = new ConditionalRule($trueCallback);
        $falseRule = new ConditionalRule($falseCallback);
        $this->assertTrue($trueRule->passes("foo"));
        $this->assertTrue($falseRule->passes("foo"));
    }

    /**
     * Tests that rules always pass when the condition returns false
     */
    public function testRulesAwaysPassWhenConditionReturnsFalse()
    {
        $callback = function () {
            return false;
        };
        $rule = new ConditionalRule($callback);
        /** @var IRule|\PHPUnit_Framework_MockObject_MockObject $extraRule */
        $extraRule = $this->getMock(IRule::class);
        $extraRule->expects($this->any())
            ->method("passes")
            ->willReturn(false);
        $rule->addRule($extraRule);
        $this->assertTrue($rule->passes("foo"));
    }
}