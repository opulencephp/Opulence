<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Validation\Tests\Rules;

use BadMethodCallException;
use Countable;
use DateTime;
use LogicException;
use Opulence\Validation\Rules\Errors\Compilers\ICompiler;
use Opulence\Validation\Rules\Errors\ErrorTemplateRegistry;
use Opulence\Validation\Rules\IRule;
use Opulence\Validation\Rules\IRuleWithArgs;
use Opulence\Validation\Rules\RuleExtensionRegistry;
use Opulence\Validation\Rules\Rules;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the rules
 */
class RulesTest extends \PHPUnit\Framework\TestCase
{
    private Rules $rules;
    /** @var RuleExtensionRegistry|MockObject The rule extension registry */
    private RuleExtensionRegistry $ruleExtensionRegistry;
    /** @var ErrorTemplateRegistry|MockObject The error template registry */
    private ErrorTemplateRegistry $errorTemplateRegistry;
    /** @var ICompiler|MockObject The error template compiler */
    private ICompiler $errorTemplateCompiler;

    protected function setUp(): void
    {
        $this->ruleExtensionRegistry = $this->createMock(RuleExtensionRegistry::class);
        $this->errorTemplateRegistry = $this->createMock(ErrorTemplateRegistry::class);
        $this->errorTemplateCompiler = $this->createMock(ICompiler::class);
        $this->rules = new Rules(
            $this->ruleExtensionRegistry,
            $this->errorTemplateRegistry,
            $this->errorTemplateCompiler
        );
    }

    /**
     * Tests the alpha-numeric rule
     */
    public function testAlphaNumericRule(): void
    {
        $this->assertSame($this->rules, $this->rules->alphaNumeric());
        $this->assertTrue($this->rules->pass('a1'));
        $this->assertFalse($this->rules->pass('a 1'));
    }

    public function testAlphaRule(): void
    {
        $this->assertSame($this->rules, $this->rules->alpha());
        $this->assertTrue($this->rules->pass('a'));
        $this->assertFalse($this->rules->pass('a1'));
    }

    public function testBetweenRule(): void
    {
        $this->assertSame($this->rules, $this->rules->between(1, 2, false));
        $this->assertFalse($this->rules->pass(1));
        $this->assertFalse($this->rules->pass(2));
        $this->assertTrue($this->rules->pass(1.5));
    }

    public function testCallingExtension(): void
    {
        $this->ruleExtensionRegistry->expects($this->once())
            ->method('hasRule')
            ->with('foo')
            ->willReturn(true);
        $rule = $this->createMock(IRule::class);
        $this->ruleExtensionRegistry->expects($this->once())
            ->method('getRule')
            ->willReturn($rule);
        $rule->expects($this->once())
            ->method('passes')
            ->with('bar')
            ->willReturn(true);
        $this->assertSame($this->rules, $this->rules->foo());
        $this->assertTrue($this->rules->pass('bar'));
    }

    public function testCallingExtensionWithArgs(): void
    {
        $this->ruleExtensionRegistry->expects($this->once())
            ->method('hasRule')
            ->with('foo')
            ->willReturn(true);
        $rule = $this->createMock(IRuleWithArgs::class);
        $rule->expects($this->once())
            ->method('setArgs')
            ->with(['baz']);
        $this->ruleExtensionRegistry->expects($this->once())
            ->method('getRule')
            ->willReturn($rule);
        $rule->expects($this->once())
            ->method('passes')
            ->with('bar')
            ->willReturn(true);
        $this->assertSame($this->rules, $this->rules->foo('baz'));
        $this->assertTrue($this->rules->pass('bar'));
    }

    /**
     * Tests calling non-existent extension
     */
    public function testCallingNonExistentExtension(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->ruleExtensionRegistry->expects($this->once())
            ->method('hasRule')
            ->with('foo')
            ->willReturn(false);
        $this->rules->foo('bar');
    }

    public function testCheckingRulesTwiceDoesNotAppendErrors(): void
    {
        $this->errorTemplateRegistry->expects($this->exactly(2))
            ->method('getErrorTemplate')
            ->with('the-field', 'email')
            ->willReturn('');
        $this->errorTemplateCompiler->expects($this->exactly(2))
            ->method('compile')
            ->with('the-field', '', [])
            ->willReturn('The error');
        $this->rules->email();
        $this->rules->pass('foo');
        $this->assertEquals(['The error'], $this->rules->getErrors('the-field'));
        $this->rules->pass('foo');
        $this->assertEquals(['The error'], $this->rules->getErrors('the-field'));
    }

    public function testConditionalRulesErrorsAreAdded(): void
    {
        $this->errorTemplateRegistry->expects($this->at(0))
            ->method('getErrorTemplate')
            ->with('the-field', 'equals')
            ->willReturn('equals template');
        $this->errorTemplateRegistry->expects($this->at(1))
            ->method('getErrorTemplate')
            ->with('the-field', 'email')
            ->willReturn('email template');
        $this->errorTemplateCompiler->expects($this->at(0))
            ->method('compile')
            ->with('the-field', 'equals template', [])
            ->willReturn('equals error');
        $this->errorTemplateCompiler->expects($this->at(1))
            ->method('compile')
            ->with('the-field', 'email template', [])
            ->willReturn('email error');
        $this->rules->condition(function () {
            return true;
        });
        $this->rules->equals('foo');
        $this->rules->email();
        $this->rules->pass('bar');
        $this->assertEquals(
            ['equals error', 'email error'],
            $this->rules->getErrors('the-field')
        );
    }

    /*
     * Tests the date rule
     */
    public function testDateRule(): void
    {
        $format1 = 'Y-m-d';
        $format2 = 'F j';
        $this->assertSame($this->rules, $this->rules->date([$format1, $format2]));
        $this->assertTrue($this->rules->pass((new DateTime)->format($format1)));
        $this->assertTrue($this->rules->pass((new DateTime)->format($format2)));
    }

    public function testEmailRule(): void
    {
        $this->assertSame($this->rules, $this->rules->email());
        $this->assertTrue($this->rules->pass('foo@bar.com'));
    }

    public function testEqualsFieldRule(): void
    {
        $this->assertSame($this->rules, $this->rules->equalsField('bar'));
        $this->assertTrue($this->rules->pass('baz', ['bar' => 'baz']));
    }

    /*
     * Tests the equals rule
     */
    public function testEqualsRule(): void
    {
        $this->assertSame($this->rules, $this->rules->equals('bar'));
        $this->assertTrue($this->rules->pass('bar'));
    }

    public function testExceptionThrownWhenNestingConditions(): void
    {
        $this->expectException(LogicException::class);
        $this->rules->condition(function () {
        });
        $this->rules->condition(function () {
        });
    }

    public function testGettingErrorsWhenThereAreNone(): void
    {
        $this->assertEquals([], $this->rules->getErrors('foo'));
        $this->rules->email();
        $this->rules->pass('foo@bar.com');
        $this->assertEquals([], $this->rules->getErrors('foo'));
    }

    public function testHaltingFieldValidationDoesNothingOnPassingRules(): void
    {
        /** @var IRule|MockObject $rule1 */
        $rule1 = $this->createMock(IRule::class);
        /** @var IRule|MockObject $rule2 */
        $rule2 = $this->createMock(IRule::class);
        $rule1->expects($this->once())
            ->method('passes')
            ->willReturn(true);
        $rule2->expects($this->once())
            ->method('passes')
            ->willReturn(true);
        $this->ruleExtensionRegistry->expects($this->at(0))
            ->method('hasRule')
            ->with('foo')
            ->willReturn(true);
        $this->ruleExtensionRegistry->expects($this->at(1))
            ->method('getRule')
            ->with('foo')
            ->willReturn($rule1);
        $this->ruleExtensionRegistry->expects($this->at(2))
            ->method('hasRule')
            ->with('bar')
            ->willReturn(true);
        $this->ruleExtensionRegistry->expects($this->at(3))
            ->method('getRule')
            ->with('bar')
            ->willReturn($rule2);
        $this->rules->foo();
        $this->rules->bar();
        $this->assertTrue($this->rules->pass('blah', [], true));
    }

    public function testHaltingFieldValidationOnFailure(): void
    {
        /** @var IRule|MockObject $rule1 */
        $rule1 = $this->createMock(IRule::class);
        /** @var IRule|MockObject $rule2 */
        $rule2 = $this->createMock(IRule::class);
        $rule1->expects($this->once())
            ->method('passes')
            ->willReturn(false);
        $rule2->expects($this->never())
            ->method('passes');
        $this->ruleExtensionRegistry->expects($this->at(0))
            ->method('hasRule')
            ->with('foo')
            ->willReturn(true);
        $this->ruleExtensionRegistry->expects($this->at(1))
            ->method('getRule')
            ->with('foo')
            ->willReturn($rule1);
        $this->ruleExtensionRegistry->expects($this->at(2))
            ->method('hasRule')
            ->with('bar')
            ->willReturn(true);
        $this->ruleExtensionRegistry->expects($this->at(3))
            ->method('getRule')
            ->with('bar')
            ->willReturn($rule2);
        $this->rules->foo();
        $this->rules->bar();
        $this->assertFalse($this->rules->pass('blah', [], true));
    }

    public function testIPAddressRule(): void
    {
        $this->assertSame($this->rules, $this->rules->ipAddress());
        $this->assertTrue($this->rules->pass('127.0.0.1'));
    }

    public function testInRule(): void
    {
        $this->assertSame($this->rules, $this->rules->in(['foo', 'bar']));
        $this->assertTrue($this->rules->pass('bar'));
    }

    public function testIntegerRule(): void
    {
        $this->assertSame($this->rules, $this->rules->integer());
        $this->assertTrue($this->rules->pass(1));
    }

    public function testMaxRule(): void
    {
        $this->assertSame($this->rules, $this->rules->max(2, false));
        $this->assertFalse($this->rules->pass(2));
        $this->assertTrue($this->rules->pass(1.9));
    }

    public function testMinRule(): void
    {
        $this->assertSame($this->rules, $this->rules->min(2, false));
        $this->assertFalse($this->rules->pass(2));
        $this->assertTrue($this->rules->pass(2.1));
    }

    /**
     * Tests that a non-required field passes all rules when empty
     */
    public function testNonRequiredFieldPassesAllRulesWhenEmpty(): void
    {
        $this->rules
            ->email()
            ->date('Y-m-d');
        $this->assertTrue($this->rules->pass(null));
        $this->assertTrue($this->rules->pass([]));
        $countable = $this->createMock(Countable::class);
        $countable->expects($this->exactly(2))
            ->method('count')
            ->willReturn(0);
        $this->assertTrue($this->rules->pass($countable));
    }

    /**
     * Tests the not-in rule
     */
    public function testNotInRule(): void
    {
        $this->assertSame($this->rules, $this->rules->notIn(['foo', 'bar']));
        $this->assertTrue($this->rules->pass('baz'));
    }

    public function testNumericRule(): void
    {
        $this->assertSame($this->rules, $this->rules->numeric());
        $this->assertTrue($this->rules->pass(1.5));
    }

    public function testRegexRule(): void
    {
        $this->assertSame($this->rules, $this->rules->regex('/^[a-z]{3}$/'));
        $this->assertTrue($this->rules->pass('baz'));
    }

    public function testRequiredRule(): void
    {
        $this->assertSame($this->rules, $this->rules->required());
        $this->assertTrue($this->rules->pass('bar'));
    }

    public function testRuleExtensionsInConditionAreRespected(): void
    {
        $this->ruleExtensionRegistry->expects($this->once())
            ->method('hasRule')
            ->with('foo')
            ->willReturn(true);
        $rule = $this->createMock(IRule::class);
        $rule->expects($this->never())
            ->method('passes');
        $this->ruleExtensionRegistry->expects($this->once())
            ->method('getRule')
            ->with('foo')
            ->willReturn($rule);
        $this->rules->condition(function () {
            return false;
        })
            ->foo();
        $this->assertTrue($this->rules->pass('bar'));
    }

    public function testRulesAddedAfterConditionAreAlwaysRespected(): void
    {
        $this->rules->required()
            ->condition(function () {
                return false;
            })
            ->email()
            ->endCondition()
            ->equals('bar');
        $this->assertTrue($this->rules->pass('bar'));
    }

    public function testRulesAddedBeforeConditionAreAlwaysRespected(): void
    {
        $this->rules->required()
            ->condition(function () {
                return false;
            })
            ->email();
        $this->assertTrue($this->rules->pass('bar'));
    }

    public function testRulesInConditionAreRespected(): void
    {
        $this->rules->condition(function () {
            return true;
        })
            ->email();
        $this->assertTrue($this->rules->pass('foo@bar.com'));
        $this->assertFalse($this->rules->pass('bar'));
    }

    public function testsPassesWithNoRules(): void
    {
        $this->assertTrue($this->rules->pass('bar'));
    }
}
