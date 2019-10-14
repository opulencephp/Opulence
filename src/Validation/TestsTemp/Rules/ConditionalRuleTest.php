<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Validation\TestsTemp\Rules;

use InvalidArgumentException;
use LogicException;
use Opulence\Validation\Rules\ConditionalRule;
use Opulence\Validation\Rules\IRule;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the conditional rule
 */
class ConditionalRuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that it fails when the condition is true and an extra rule fails
     */
    public function TestFailsWhenConditionTrueAndExtraRuleFails(): void
    {
        $callback = function () {
            return true;
        };
        $rule = new ConditionalRule();
        $rule->setArgs([$callback]);
        /** @var IRule|MockObject $extraRule */
        $extraRule = $this->createMock(IRule::class);
        $extraRule->expects($this->once())
            ->method('passes')
            ->willReturn(false);
        $rule->addRule($extraRule);
        $this->assertFalse($rule->passes('foo'));
    }

    public function testGettingSlug(): void
    {
        $rule = new ConditionalRule();
        $this->assertEquals('conditional', $rule->getSlug());
    }

    /**
     * Tests getting sub-rules
     */
    public function testGettingSubRules(): void
    {
        /** @var IRule|MockObject $subRule1 */
        $subRule1 = $this->createMock(IRule::class);
        /** @var IRule|MockObject $subRule2 */
        $subRule2 = $this->createMock(IRule::class);
        $rule = new ConditionalRule();
        $rule->addRule($subRule1);
        $rule->addRule($subRule2);
        $this->assertEquals([$subRule1, $subRule2], $rule->getRules());
    }

    public function testNotSettingArgBeforePasses(): void
    {
        $this->expectException(LogicException::class);
        $rule = new ConditionalRule();
        $rule->passes('foo');
    }

    public function testPassesWithNoExtraRules(): void
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

    public function testPassingEmptyArgArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new ConditionalRule();
        $rule->setArgs([]);
    }

    public function testPassingInvalidArg(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $rule = new ConditionalRule();
        $rule->setArgs(['foo']);
    }

    public function testRulesAwaysPassWhenConditionReturnsFalse(): void
    {
        $callback = function () {
            return false;
        };
        $rule = new ConditionalRule();
        $rule->setArgs([$callback]);
        /** @var IRule|MockObject $extraRule */
        $extraRule = $this->createMock(IRule::class);
        $extraRule->expects($this->any())
            ->method('passes')
            ->willReturn(false);
        $rule->addRule($extraRule);
        $this->assertTrue($rule->passes('foo'));
    }
}
